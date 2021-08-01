<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use XLite\Model\Cart;
use XLite\Model\Profile;

/**
 * Authorization routine
 */
class Auth extends \XLite\Base
{
    /**
     * Result codes
     */
    const RESULT_USER_EXISTS          = 1;
    const RESULT_REGISTER_SUCCESS     = 2;
    const RESULT_ACCESS_DENIED        = 3;
    const RESULT_LAST_ADMIN_ACCOUNT   = 4;
    const RESULT_LOGIN_IS_LOCKED      = 5;
    const RESULT_PROFILE_IS_ANONYMOUS = 6;
    const RESULT_PASSWORD_NOT_EQUAL   = 7;
    const RESULT_INVALID_SECURE_HASH  = 8;

    /**
     * Session var name to keep the secret token
     */
    const SESSION_SECURE_HASH_CELL = 'secureHashCell';

    /**
     * Default hash algorithm
     */
    const DEFAULT_HASH_ALGO = 'SHA512';

    /**
     * Max count of login attempt
     */
    const MAX_COUNT_OF_LOGIN_ATTEMPTS = 5;

    /**
     * Time of lock login (sec)
     */
    const TIME_OF_LOCK_LOGIN = 120;

    /**
     * Recent login cookie name 
     */
    const RECENT_LOGIN_COOKIE_NAME = 'recent_login';

    /**
     * The list of session vars that must be cleared on logoff
     *
     * @var array
     */
    protected $sessionVarsToClear = array(
        'profile_id',
        'anonymous',
        'closedBlocks',
        'createProfilePassword',
        'dateOfLockLogin',
    );

    /**
     * Profile (cache)
     *
     * @var array
     */
    protected $profile;

    /**
     * Membership identificator of profile (cache)
     *
     * @var integer
     */
    protected $membershipId;

    /**
     * Encrypts password (calculates MD5 hash)
     *
     * @param string $hash     Hash
     * @param string $password Password string to encrypt
     *
     * @return string
     */
    public static function comparePassword($hash, $password)
    {
        $parts = explode(':', $hash, 2);
        if (1 == count($parts)) {
            $algo = 'MD5';

        } else {
            list($algo, $hash) = $parts;
        }

        return static::encryptPassword($password, $algo) == $algo . ':' . $hash;
    }

    /**
     * Encrypts password (calculates MD5 hash)
     *
     * @param string $password Password string to encrypt
     * @param string $algo     Algorithm OPTIONAL
     *
     * @return string
     */
    public static function encryptPassword($password, $algo = self::DEFAULT_HASH_ALGO)
    {
        $method = 'encryptPassword' . $algo;
        if (!method_exists(get_called_class(), $method)) {
            $algo = static::DEFAULT_HASH_ALGO;
            $method = 'encryptPassword' . $algo;
        }

        // Method name assembled from 'encryptPassword' + $algo
        $encrypted = static::$method($password);

        return $algo . ':' . $encrypted;
    }

    /**
     * Encrypts password (calculates SHA512 hash)
     *
     * @param string $password Password string to encrypt
     *
     * @return string
     */
    protected static function encryptPasswordSHA512($password)
    {
        if (\XLite::getInstance()->getOptions(array('installer_details', 'shared_secret_key'))) {
            $result = hash_hmac(
                'sha512',
                $password,
                strval(\XLite::getInstance()->getOptions(array('installer_details', 'shared_secret_key')))
            );

        } else {
            $result = hash('sha512', $password);
        }

        return $result;
    }

    /**
     * Encrypts password (calculates SHA512 hash)
     *
     * @param string $password Password string to encrypt
     *
     * @return string
     */
    protected static function encryptPasswordMD5($password)
    {
        return md5($password);
    }

    /**
     * Updates the specified profile on login. Saves profile to session
     *
     * @param Profile $profile Profile object
     *
     * @return boolean
     */
    public function loginProfile(Profile $profile, $withCells = true)
    {
        $result = $profile->isPersistent();

        if ($result) {

            // Restart session
            Session::getInstance()->restart($withCells);

            $loginTime = Converter::time();

            // Check for the first time login
            if (!$profile->getFirstLogin()) {
                // Set first login date
                $profile->setFirstLogin($loginTime);
            }

            $profile->setCountOfLoginAttempts(0);
            $profile->setDateOfLoginAttempt(0);

            // Set last login date
            $profile->setLastLogin($loginTime);

            // Update profile
            $profile->update();

            // Save profile Id in session
            Session::getInstance()->profile_id = $profile->getProfileId();
            Session::getInstance()->forceChangePassword = $profile->getForceChangePassword();

            // Save login in cookies
            $this->rememberLogin($profile->getLogin());
        }

        return $result;
    }

    /**
     * Add variable to the list of session vars that must be cleared on logoff
     *
     * @param string $name Session variable name
     */
    public function addSessionVarToClear($name)
    {
        $this->sessionVarsToClear[] = $name;
    }

    /**
     * Returns the list of session vars that must be cleared on logoff
     *
     * @return array
     */
    public function getSessionVarsToClear()
    {
        return $this->sessionVarsToClear;
    }

    /**
     * Check login and password
     *
     * @param string $login      User's login
     * @param string $password   User's password
     * @param string $secureHash Secret token OPTIONAL
     *
     * @return array
     */
    public function checkLoginPassword($login, $password, $secureHash = null)
    {
        $profile = null;
        $result = self::RESULT_ACCESS_DENIED;

        // Check for the valid parameters
        if (!empty($login) && !empty($password)) {

            if (isset($secureHash) && !$this->checkSecureHash($secureHash)) {
                $result = static::RESULT_INVALID_SECURE_HASH;

            } else {
                // Initialize order Id
                $orderId = Request::getInstance()->anonymous
                    ? Cart::getInstance()->getOrderId()
                    : 0;

                // Try to get user profile by login and order id
                $profile = Database::getRepo('XLite\Model\Profile')->findByLoginPassword(
                    $login,
                    null,
                    $orderId
                );

                // Check annonymous state - anonymous use cannot login
                if (isset($profile) && !$orderId && $profile->getAnonymous()) {
                    $result = static::RESULT_PROFILE_IS_ANONYMOUS;

                // Check password - password has must equal
                } elseif (isset($profile) && !isset($secureHash) && !static::comparePassword($profile->getPassword(), $password)) {
                    $result = static::RESULT_PASSWORD_NOT_EQUAL;

                } elseif (isset($profile)) {
                    $result = true;
                }

                if ($profile && $profile->isAdmin()) {
                    if (Converter::time() < (Session::getInstance()->dateOfLockLogin + static::TIME_OF_LOCK_LOGIN)) {
                        $result = static::RESULT_LOGIN_IS_LOCKED;

                    } elseif (
                        $profile
                        && Converter::time() < ($profile->getDateOfLoginAttempt() + static::TIME_OF_LOCK_LOGIN)
                        && static::MAX_COUNT_OF_LOGIN_ATTEMPTS <= $profile->getCountOfLoginAttempts()
                    ) {
                        Session::getInstance()->dateOfLockLogin = $profile->getDateOfLoginAttempt();
                        $result = static::RESULT_LOGIN_IS_LOCKED;
                    }
                }
            }
        }

        return array($profile, $result);
    }

    /**
     * Logs in user to cart
     *
     * @param string $login      User's login
     * @param string $password   User's password
     * @param string $secureHash Secret token OPTIONAL
     *
     * @return Profile|integer
     */
    public function login($login, $password, $secureHash = null)
    {
        $result = static::RESULT_ACCESS_DENIED;

        if (!empty($login) && !empty($password)) {

            list($profile, $result) = $this->checkLoginPassword($login, $password, $secureHash);

            if ($result === static::RESULT_INVALID_SECURE_HASH) {
                // TODO - potential attack; send the email to admin
                $this->doDie('Trying to log in using an invalid secure hash string.');
            }

            if (isset($profile) && $result === static::RESULT_PASSWORD_NOT_EQUAL) {
                $countOfLoginAttempts = Converter::time() < ($profile->getDateOfLoginAttempt() + static::TIME_OF_LOCK_LOGIN)
                    ? $profile->getCountOfLoginAttempts() + 1
                    : 1;
                $profile->setCountOfLoginAttempts($countOfLoginAttempts);
                $profile->setDateOfLoginAttempt(Converter::time());
                $profile->update();

                if (\XLite::isAdminZone() && static::MAX_COUNT_OF_LOGIN_ATTEMPTS <= $profile->getCountOfLoginAttempts()) {
                    Session::getInstance()->dateOfLockLogin = Converter::time();
                    Mailer::sendFailedAdminLoginAdmin($profile->getLogin());
                }
                $profile = null;
            }

            if ($result === static::RESULT_LOGIN_IS_LOCKED) {
                $profile = null;
            }

            // Check annonymous state - anonymous use cannot login
            if ($result === static::RESULT_PROFILE_IS_ANONYMOUS) {
                $profile = null;
            }

            // Initialize order Id
            $orderId = Request::getInstance()->anonymous
                ? Cart::getInstance()->getOrderId()
                : 0;

            // Return profile object if it's ok
            if (isset($profile) && $this->loginProfile($profile)) {

                \XLite\Core\Session::getInstance()->setLanguage($profile->getLanguage(), 'admin');
                \XLite\Core\Session::getInstance()->setLanguage($profile->getLanguage(), 'customer');

                // Rewrite password hash if current hash is obsolete
                if (!isset($secureHash) && $password && $profile->getPasswordAlgo() != static::DEFAULT_HASH_ALGO) {
                    $profile->setPassword(static::encryptPassword($password));
                }

                $result = $profile;

                // Renew order
                $orderId = $orderId ?: Session::getInstance()->order_id;
                $order = Database::getRepo('XLite\Model\Cart')->find($orderId);
                if ($order) {
                    $order->renew();
                }
            }
        }

        // Invalidate cache
        $this->resetProfileCache();

        return $result;
    }

    /**
     * Logs off the currently logged profile
     *
     * @throws \Exception
     */
    public function logoff()
    {
        $session = Session::getInstance();
        $session->last_profile_id = $session->profile_id;
        $session->resetFormId();

        $this->clearSessionVars();

        // Invalidate cache
        $this->resetProfileCache();

        Session::getInstance()->forceChangePassword = false;

        Database::getEM()->flush();
    }

    /**
     * Checks whether user is logged
     *
     * @return boolean
     */
    public function isLogged()
    {
        return !is_null($this->getProfile());
    }

    /**
     * Checks whether user is anonymous one
     *
     * @return boolean
     */
    public function isAnonymous()
    {
        $profile = $this->getProfile();
        return $profile ? $profile->getAnonymous() : false;
    }

    /**
     * Finish "Operating as" mode
     */
    public function finishOperatingAs()
    {
        Session::getInstance()->operating_as_profile_id = null;
        unset(Session::getInstance()->order_id);
    }

    /**
     * Set "Operating as" profile id
     *
     * @param Profile $profile Profile
     */
    public function setOperatingAs(Profile $profile)
    {
        Session::getInstance()->operating_as_profile_id = $profile->getProfileId();
        unset(Session::getInstance()->order_id);
    }

    /**
     * Get "Operating as" profile id
     *
     * @return integer
     */
    public function getOperatingAs()
    {
        return Session::getInstance()->operating_as_profile_id;
    }

    /**
     * Check if profile in operating as user mode
     * 
     * @return boolean
     */
    public function isOperatingAsUserMode()
    {
        return (bool) $this->getOperatingAs()
            && $this->getProfile()
            && $this->getProfile()->getProfileId() === $this->getOperatingAs();
    }

    /**
     * Get Operate as user payment method
     *
     * @return array
     */
    public function getOperateAsUserPaymentMethods()
    {
        $phoneOrdering = Database::getRepo('XLite\Model\Payment\Method')->findOneBy(
            array(
                'service_name' => 'PhoneOrdering'
            )
        );

        return array_filter([
            $phoneOrdering
        ]);
    }

    /**
     * Get profile registered in session
     *
     * @param integer $profileId Profile Id OPTIONAL
     *
     * @return Profile
     */
    public function getProfile($profileId = null)
    {
        if (isset($profileId)) {
            $profile = Database::getRepo('XLite\Model\Profile')->find($profileId);
            $result = $this->checkProfile($profile) ? $profile : null;

        } else {

            if (!$this->profile['isInitialized']) {
                $this->resetProfileCache();
                $this->profile['isInitialized'] = true;

                $entity = null;

                if ((bool) $this->getOperatingAs() && !\XLite::isAdminScript()) {
                    $entity = Database::getRepo('XLite\Model\Profile')->find($this->getOperatingAs());
                }

                if (!$entity) {
                    if ((bool) $this->getOperatingAs()) {
                        TopMessage::addInfo('Finished operating as user');
                        $this->finishOperatingAs();
                    }

                    $entity = Database::getRepo('XLite\Model\Profile')->find($this->getStoredProfileId());
                }

                $this->profile['object'] = $entity;

                if (\XLite::isAdminZone() && $entity && $this->isAdmin($this->getProfile())) {
                    $this->rememberLogin($entity->getLogin());
                    \Includes\Utils\Session::setAdminCookie();
                }
            }

            $result = $this->profile['object'];
        }

        return $result;
    }

    /**
     * Return membership of active profile
     *
     * @return integer
     */
    public function getMembershipId()
    {
        if (!isset($this->membershipId)) {
            $this->membershipId = ($this->getProfile() && $this->getProfile()->getMembership())
                ? $this->getProfile()->getMembership()->getMembershipId()
                : null;
        }

        return $this->membershipId;
    }

    /**
     * Return membership of active profile
     *
     * @return \XLite\Model\AEntity|\XLite\Model\Membership|null
     */
    public function getMembership()
    {
        return $this->getMembershipId()
            ? Database::getRepo('XLite\Model\Membership')->find($this->getMembershipId())
            : null;
    }

    /**
     * Check if passed profile is currently logged in
     * @todo It could be a bug since it always be true if the currently logged user is admin and profile - any profile
     *
     * @param Profile $profile Profile to check OPTIONAL
     *
     * @return boolean
     */
    public function checkProfile(Profile $profile = null)
    {
        return $this->isLogged() && $profile && $this->checkProfileAccessibility($profile);
    }

    /**
     * Checks whether the currently logged user is an administrator
     *
     * @param Profile $profile User profile OPTIONAL
     *
     * @return boolean
     */
    public function isAdmin(Profile $profile = null)
    {
        if (!isset($profile)) {
            $profile = $this->getProfile();
        }

        return $profile
            && $profile->getAccessLevel() === $this->getAdminAccessLevel();
    }

    /**
     * Return true if profile has permissions to manage admin profiles
     *
     * @param Profile $profile User profile OPTIONAL
     *
     * @return boolean
     */
    public function isAdminProfilesManager(Profile $profile = null)
    {
        if (!isset($profile)) {
            $profile = $this->getProfile();
        }

        return $profile
            && $this->isAdmin($profile)
            && $profile->isPermissionAllowed('manage admins');
    }

    /**
     * Return access level for the passed user type
     *
     * @param string $type Profile type (see getUserTypes() for list of allowed values)
     *
     * @return integer
     */
    public function getAccessLevel($type)
    {
        return in_array($type, $this->getUserTypes())
            ? call_user_func(array($this, 'get' . $type . 'Accesslevel'))
            : null;
    }

    /**
     * Gets the access level for administrator
     *
     * @return integer
     */
    public function getAdminAccessLevel()
    {
        return 100;
    }

    /**
     * Gets the access level for a customer
     *
     * @return integer
     */
    public function getCustomerAccessLevel()
    {
        return 0;
    }

    /**
     * Returns all user types configured for this system
     *
     * @return array
     */
    public function getUserTypes()
    {
        return array(
            'customer' => 'Customer',
            'admin'    => 'Admin',
        );
    }

    /**
     * Return list of all allowed access level values (by default - array(0, 100))
     *
     * @return array
     */
    public function getAccessLevelsList()
    {
        return array_map(array($this, 'getAccessLevel'), $this->getUserTypes());
    }

    /**
     * getUserTypesRaw
     *
     * @return array
     */
    public function getUserTypesRaw()
    {
        return array_combine($this->getAccessLevelsList(), $this->getUserTypes());
    }

    /**
     * Save the secret token in session.
     * See "checkSecureHash()" method
     *
     * @param string $hashString Hash string to save
     */
    public function setSecureHash($hashString)
    {
        $cell = self::SESSION_SECURE_HASH_CELL;
        Session::getInstance()->$cell = $hashString;
    }

    /**
     * Remind recent login from cookies
     *
     * @return string
     */
    public function remindLogin()
    {
        return empty($_COOKIE[static::RECENT_LOGIN_COOKIE_NAME]) ? '' : $_COOKIE[static::RECENT_LOGIN_COOKIE_NAME];
    }

    /**
     * Logs in admin to cart.
     *
     * @param string $login    Administrator user login
     * @param string $password Administrator user password
     *
     * @return Profile
     * @throws \Exception
     */
    public function loginAdministrator($login, $password)
    {
        $profile = $this->login($login, $password);

        if ($profile instanceof Profile && !$profile->isAdmin()) {

            // Logoff user from session
            $this->logoff();

            // Reset profile object
            $profile = static::RESULT_ACCESS_DENIED;

            // Send notification about failed log in attempt
            Mailer::sendFailedAdminLoginAdmin(Request::getInstance()->login);
        }

        return $profile;
    }

    /**
     * Checks whether user has enough permissions to access specified resource.
     * Resource should provide access to "getAccessLevel()" method in order
     * to check authority.
     *
     * @param \XLite\Base $resource Resource
     *
     * @return boolean
     */
    public function isAuthorized(\XLite\Base $resource)
    {
        // Check whether resource is valid (has getAccessLevel() method)
        if (!method_exists($resource, 'getAccessLevel')) {
            $this->doDie('Auth::isAuthorized(): Authorization failed: resource invalid');
        }

        $profile = $this->getProfile();

        $currentLevel = $profile ? $profile->getAccessLevel() : 0;

        return $currentLevel >= $resource->getAccessLevel();
    }

    /**
     * Reset default values for the "profile" property
     */
    protected function resetProfileCache()
    {
        $this->profile = array('isInitialized' => false, 'object' => null);
    }

    /**
     * User can access profile only in two cases:
     * 1) he/she is an admin
     * 2) its the user's own account
     *
     * @param Profile $profile Profile to check
     *
     * @return boolean
     */
    protected function checkProfileAccessibility(Profile $profile)
    {
        return (\XLite::isAdminZone() && $this->isAdmin($this->getProfile())) || $this->getProfile()->getProfileId() == $profile->getProfileId();
    }

    /**
     * Clear some session variables on logout
     */
    protected function clearSessionVars()
    {
        foreach ($this->getSessionVarsToClear() as $name) {
            unset(Session::getInstance()->$name);
        }
    }

    /**
     * Check if passed string is equal to the hash, previously saved in session.
     * It's the secure mechanism to login using the secret hash (e.g. login anonymous user)
     *
     * @param string $hashString String to check
     *
     * @return boolean
     */
    protected function checkSecureHash($hashString)
    {
        $result = false;

        $cell = self::SESSION_SECURE_HASH_CELL;

        if (!empty($hashString)) {
            $result = Session::getInstance()->$cell === $hashString;
        }

        // Using this method, it's not possible to log in several times
        unset(Session::getInstance()->$cell);

        return $result;
    }

    /**
     * Remember login in cookie
     *
     * @param mixed $login User's login
     */
    protected function rememberLogin($login)
    {
        Request::getInstance()->setCookie(
            static::RECENT_LOGIN_COOKIE_NAME,
            $login,
            86400 * intval(Config::getInstance()->General->login_lifetime)
        );
    }

    /**
     * Get stored profile id
     *
     * @return integer
     */
    protected function getStoredProfileId()
    {
        return Session::getInstance()->profile_id;
    }

    /**
     * Protected constructor
     */
    protected function __construct()
    {
        parent::__construct();

        $this->resetProfileCache();
    }

    // {{{ Roles and permissions

    /**
     * Check - specified permission is allowed or not
     *
     * @param string $code Permission code
     *
     * @return boolean
     */
    public function isPermissionAllowed($code)
    {
        $profile = $this->getProfile();

        return $profile && $profile->isPermissionAllowed($code);
    }

    /**
     * Check if current user has root access permission
     *
     * @return boolean
     */
    public function hasRootAccess()
    {
        return $this->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }

    /**
     * Check - specified permission is allowed or not
     *
     * @param string|array $code Permission code(s)
     *
     * @return boolean
     */
    public function isPermissionAllowedOr($code)
    {
        $profile = $this->getProfile();

        return $profile && call_user_func_array(array($profile, __METHOD__), func_get_args());
    }

    // }}}

    // {{{ Storefront accessibility routines

    /**
     * Returns TRUE if the storefront is accessible for the current customer
     * It looks into the session and checks if the valid shop key is present
     *
     * @return boolean
     */
    public function isAccessibleStorefront()
    {
        return !$this->isClosedStorefront()
            || ($this->getRequestShopKey() == $this->getShopKey());
    }

    /**
     * Returns the internal flag of storefront access
     * The admin can close or open the storefront via admin menu
     *
     * @return boolean
     */
    public function isClosedStorefront()
    {
        return 'Y' == Config::getInstance()->Internal->shop_closed;
    }

    /**
     * Clears the storefront closed flag
     * Clears the special access shop key value
     * @throws \Exception
     */
    public function openStorefront()
    {
        $this->changeStorefrontActivity(true);
        $this->cleanShopKey();
        \XLite\Core\Config::updateInstance();

        \XLite\Core\Marketplace::getInstance()->setStorefrontActivity(
            true,
            $this->getShopKey()
        );
    }

    /**
     * Sets the storefront closed flag
     * Generates the special access shop key value
     * @throws \Exception
     */
    public function closeStorefront()
    {
        $this->generateShopKey();
        $this->changeStorefrontActivity(false);
        \XLite\Core\Config::updateInstance();

        \XLite\Core\Marketplace::getInstance()->setStorefrontActivity(
            false,
            $this->getShopKey()
        );
    }

    /**
     * Returns the special access shop key
     *
     * @return string
     */
    public function getShopKey()
    {
        return Config::getInstance()->Internal->shop_key;
    }

    /**
     * Change storefront activity
     *
     * @param boolean $status Activity status OPTIONAL
     *
     * @throws \Exception
     */
    protected function changeStorefrontActivity($status = true)
    {
        Database::getRepo('\XLite\Model\Config')->createOption(
            array(
                'category' => 'Internal',
                'name'     => 'shop_closed',
                'value'    => true === $status ? 'N' : 'Y'
            )
        );
        Config::updateInstance();
    }

    /**
     * Generates the shop key
     * @throws \Exception
     */
    protected function generateShopKey()
    {
        $this->storeShopKey(md5(microtime()));
    }

    /**
     * Clears the special access shop key value
     * @throws \Exception
     */
    protected function cleanShopKey()
    {
        $this->storeShopKey();
    }

    /**
     * Store shop key method
     *
     * @param string $value Shop key value OPTIONAL
     *
     * @throws \Exception
     */
    protected function storeShopKey($value = '')
    {
        Database::getRepo('\XLite\Model\Config')->createOption(
            array(
                'category' => 'Internal',
                'name'     => 'shop_key',
                'value'    => $value,
            )
        );
    }

    /**
     * Returns the shop key from the request or session
     * If the request shop key is found
     * then the key is stored into session
     *
     * @return string
     */
    protected function getRequestShopKey()
    {
        $request = Request::getInstance();
        $session = Session::getInstance();
        $result = null;

        if ($request->shopKey) {
            $result = $session->shopKey = $request->shopKey;

        } elseif ($session->shopKey) {
            $result = $session->shopKey;
        }

        return $result;
    }

    // }}}

    // {{{ Access control routines

    /**
     * Return array of \XLite\Model\AccessControlCell
     * 
     * @return \XLite\Model\AccessControlCell[]
     */
    public function getAccessControlCells()
    {
        $session = Session::getInstance();
        
        return $session->getAccessControlCells();
    }

    /**
     * Return true if one of session Access Control Cell has access to entity
     *
     * @param $entity
     *
     * @return bool
     */
    public function checkACEAccess($entity)
    {
        foreach ($this->getAccessControlCells() as $cell) {
            if ($cell->hasEntityAccess($entity)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return true if one of session Access Control Cell has access to entity
     *
     * @param $zone
     *
     * @return bool
     */
    public function checkACZAccess($zone)
    {
        foreach ($this->getAccessControlCells() as $cell) {
            if ($cell->hasZoneAccess($zone)) {
                return true;
            }
        }

        return false;
    }

    // }}}
}
