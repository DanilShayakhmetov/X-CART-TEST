<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Current session
 */
abstract class SessionAbstract extends \XLite\Base\Singleton
{
    /**
     * Public session id argument name
     */
    const ARGUMENT_NAME = 'xid';

    /**
     * Referer cookie name
     */
    const LC_REFERER_COOKIE_NAME = 'LCRefererCookie';

    /**
     * Name of the cell to store the cURL error code value
     */
    const CURL_CODE_ERROR = 'curl_code_error_in_session';

    /**
     * Name of the cell to store the cURL error code value
     */
    const CURL_CODE_ERROR_MESSAGE = 'curl_error_message_in_session';

    /**
     * Session
     *
     * @var \XLite\Model\Session
     */
    protected $session;

    /**
     * Currently used form ID
     *
     * @var string
     */
    protected static $xliteFormId;

    /**
     * Language (cache)
     *
     * @var \XLite\Model\Language
     */
    protected $language;

    /**
     * Last form id
     *
     * @var string
     */
    protected $lastFormId;

    /**
     * @var string lazy cache
     */
    private $secondDomainSessionId;


    /**
     * Get session TTL (seconds)
     *
     * @return integer
     */
    public static function getTTL()
    {
        return 0;
    }

    /**
     * Getter
     *
     * @param string $name Session cell name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->session->$name;
    }

    /**
     * Setter
     *
     * @param string $name  Session cell name
     * @param mixed  $value Value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->session->$name = $value;
    }

    /**
     * Check session cell availability
     *
     * @param string $name Session cell name
     *
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->session->$name);
    }

    /**
     * Remove session cell
     *
     * @param string $name Session cell name
     *
     * @return void
     */
    public function __unset($name)
    {
        unset($this->session->$name);
    }

    /**
     * Getter
     * DEPRECATE
     *
     * @param string $name Session cell name
     *
     * @return mixed
     */
    public function get($name)
    {
        return $this->__get($name);
    }

    /**
     * Setter
     * DEPRECATE
     *
     * @param string $name  Session cell name
     * @param mixed  $value Value
     *
     * @return void
     */
    public function set($name, $value)
    {
        $this->__set($name, $value);
    }

    /**
     * Unset in batch mode
     *
     * @param string $name CEll name
     *
     * @return void
     */
    public function unsetBatch($name)
    {
        call_user_func_array(array($this->session, 'unsetBatch'), func_get_args());
    }

    /**
     * Reset session form id
     *
     * @return string
     */
    public function resetFormId()
    {
        if ($this->getSessionFormId()) {
            \XLite\Core\Database::getRepo('XLite\Model\FormId')->delete($this->getSessionFormId());
            return $this->createFormId(true);
        }

        return null;
    }

    /**
     * Restart session
     */
    public function restart($withCells = true)
    {
        $this->lastFormId = null;

        $dump = $this->isDump();
        if (!$dump && !\XLite\Core\Database::getEM()->contains($this->session)) {
            try {
                $this->session = \XLite\Core\Database::getEM()->merge($this->session);

            } catch (\Doctrine\ORM\EntityNotFoundException $exception) {
                $this->session = null;
            }
        }
        $old = null;

        if ($this->session) {
            $old = $this->session;
            $oldId = $this->session->getId();
        }

        $this->createSession();
        if ($old && !$dump) {
            // Copy session cells from old to new session
            if ($withCells) {
                foreach ($old->getCellsCache() as $cell) {
                    $cell->detach();
                    $cell->setSession($this->session);
                    $this->session->addCells($cell);
                    \XLite\Core\Database::getEM()->persist($cell);
                }
            }

            // Remove old session
            \XLite\Core\Database::getEM()->remove($old);
            \XLite\Core\Database::getEM()->flush();
        }

        $this->setCookie();
    }

    /**
     * Stores the cURL error code into session
     *
     * @param integer $code cURL error
     *
     * @return void
     */
    public function storeCURLError($code)
    {
        $this->{static::CURL_CODE_ERROR} = $code;
    }

    /**
     * Returns the cURL error code from session
     *
     * @return integer
     */
    public function getCURLError()
    {
        $result = $this->{static::CURL_CODE_ERROR};
        $this->storeCURLError(null);

        return $result;
    }

    /**
     * Stores the cURL error message into session
     *
     * @param string $msg cURL error message
     *
     * @return void
     */
    public function storeCURLErrorMessage($msg)
    {
        $this->{static::CURL_CODE_ERROR_MESSAGE} = $msg;
    }

    /**
     * Returns the cURL error message from session
     *
     * @return string
     */
    public function getCURLErrorMessage()
    {
        $result = $this->{static::CURL_CODE_ERROR_MESSAGE};
        $this->storeCURLErrorMessage(null);

        return $result;
    }

    /**
     * Get public session id argument name
     *
     * @return string
     */
    public function getName()
    {
        return self::ARGUMENT_NAME;
    }

    /**
     * Get public session id
     *
     * @return string
     */
    public function getID()
    {
        return $this->session->getSid();
    }

    /**
     * Load session by public session id
     *
     * @param string $sid Public session id
     *
     * @return boolean
     */
    public function loadBySid($sid)
    {
        $session = $this->loadSession($sid);

        $result = false;

        if ($session) {
            $result = true;

            if ($session->isPersistent()) {
                \XLite\Core\Database::getEM()->remove($this->session);
                \XLite\Core\Database::getEM()->flush();
            }

            $this->session = $session;
            $this->lastFormId = null;
            $this->setCookie();
        }

        return $result;
    }

    /**
     * Get session formId
     *
     * @return string
     */
    public function getSessionFormId()
    {
        return $this->session->getFormIds()->last();
    }

    /**
     * Create form id
     *
     * @param boolean $force Flag for forcing form id creation OPTIONAL
     *
     * @return string Form id
     */
    public function createFormId($force = false)
    {
        if (!isset($this->lastFormId) || $force) {
            if ($this->getSessionId()) {

                $this->session = \XLite\Core\Database::getEM()->merge($this->session);
                $formIdStrategy = \XLite::getInstance()->getFormIdStrategy();

                if ($formIdStrategy === 'per-session') {
                    $formId = $this->getSessionFormId();
                }

                if ($formIdStrategy !== 'per-session' || !$formId) {
                    $formId = new \XLite\Model\FormId;
                    $formId->setSession($this->session);
                    $this->session->addFormIds($formId);
                    \XLite\Core\Database::getEM()->persist($formId);
                    \XLite\Core\Database::getEM()->flush($formId);
                }

                $this->lastFormId = $formId->getFormId();

            } else {
                $this->lastFormId = md5(microtime(true));
            }
        }

        return $this->lastFormId;
    }

    /**
     * Restore form id
     *
     * @return string
     */
    public function restoreFormId()
    {
        $formIdStrategy = \XLite::getInstance()->getFormIdStrategy();

        if ($formIdStrategy === 'per-session') {
            return $this->lastFormId;
        }

        $request = \XLite\Core\Request::getInstance();

        if (!empty($request->{\XLite::FORM_ID})) {

            $this->session = \XLite\Core\Database::getEM()->merge($this->session);

            $formId = new \XLite\Model\FormId;
            $formId->setFormId($request->{\XLite::FORM_ID});
            $formId->setSession($this->session);
            $this->session->addFormIds($formId);

            \XLite\Core\Database::getEM()->persist($formId);
            \XLite\Core\Database::getEM()->flush($formId);

            $this->lastFormId = $formId->getFormId();
        }

        return $this->lastFormId;
    }

    /**
     * Session ID for forms
     *
     * @return integer
     */
    public function getSessionId()
    {
        return $this->session->getId();
    }

    /**
     * Get model
     *
     * @return \XLite\Model\Session
     */
    public function getModel()
    {
        return $this->session;
    }

    /**
     * Get language
     *
     * @return \XLite\Model\Language
     */
    public function getLanguage()
    {
        if (!isset($this->language)) {
            $this->language = \XLite\Core\Database::getRepo('XLite\Model\Language')
                ->findOneByCode($this->getCurrentLanguage());
        }

        return $this->language;
    }

    /**
     * Set language
     *
     * @param string $language Language code
     * @param string $zone     Admin/customer zone OPTIONAL
     *
     * @return void
     */
    public function setLanguage($language, $zone = null)
    {
        $code = $this->__get('language');

        if (!isset($zone)) {
            $zone = \XLite::isAdminZone() ? 'admin' : 'customer';
        }

        if (!is_array($code)) {
            $code = array();
        }

        if (!isset($code[$zone]) || $code[$zone] !== $language) {
            $code[$zone] = $language;

            $this->__set('language', $code);
            $this->language = null;
        }
    }

    /**
     * Update language in customer sessions
     *
     * @return void
     */
    public function updateSessionLanguage()
    {
        $list = array();

        foreach (\XLite\Core\Database::getRepo('\XLite\Model\SessionCell')->findByName('language') as $cell) {
            $data = $cell->getValue() ?: array();

            if (isset($data['customer'])) {
                $data['customer'] = \XLite\Core\Config::getInstance()->General->default_language;
                $cell->setValue($data);

                $list[] = $cell;
            }
        }

        \XLite\Core\Database::getRepo('\XLite\Model\SessionCell')->updateInBatch($list);
    }

    /**
     * Clear (remove) sessions by profile_id
     *
     * @param integer $profileId User profile ID
     *
     * @return void
     */
    public function clearUserSession($profileId)
    {
        $sessions = \XLite\Core\Database::getRepo('\XLite\Model\Session')->findByCellValue('profile_id', $profileId);

        if ($sessions) {
            \XLite\Core\Database::getRepo('\XLite\Model\Session')->deleteInBatch($sessions);
        }
    }

    /**
     * Check - current session is dump or not
     *
     * @return boolean
     */
    public function isDump()
    {
        return $this->session instanceOf \XLite\Model\SessionDump;
    }

    /**
     * @return \XLite\Model\Session
     */
    public function createNewSession(): \XLite\Model\Session
    {
        $em = \XLite\Core\Database::getEM();

        $session = new \XLite\Model\Session();
        $session->updateExpiry();
        $session->setSid(\XLite\Core\Database::getRepo('XLite\Model\Session')->generatePublicSessionId());

        $em->persist($session);
        $em->flush();

        return $session;
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct()
    {
        if (!$this->restoreSession()) {
            $this->createSession();
        }

        $this->runCronTasks();

        $this->setCookie();
    }

    /**
     * Clear expired sessions and other obsolete data
     *
     * @return void
     */
    protected function clearGarbage()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Session')->removeExpired();
    }

    /**
     * Restore session
     *
     * @return boolean
     */
    protected function restoreSession()
    {
        $this->session = null;

        [$session, $source] = $this->detectPublicSession();

        if ($session && $session->getExpiry() >= \XLite\Core\Converter::time()) {

            $this->session = $session;
        }

        if ($this->session) {

            if ('COOKIE' === $source) {

                // DO NOT Change the current session if the $sid goes from COOKIE
                if ($this->session->updateExpiry()) {
                    \XLite\Core\Database::getEM()->flush($this->session);
                }

            } else {

                // Change the current session if the $sid goes NOT from COOKIE (POST, GET)
                $this->restart();
            }
        }

        return isset($this->session);
    }

    /**
     * Detect public session
     *
     * @return array (public session model object and source)
     */
    protected function detectPublicSession()
    {
        $sid = null;
        $source = null;
        $arg = $this->getName();
        $session = null;

        foreach (array('POST', 'GET', 'COOKIE') as $key) {
            if (isset($GLOBALS['_' . $key][$arg])) {
                $sid = $GLOBALS['_' . $key][$arg];
                $source = $key;
                break;
            }
        }

        if ('COOKIE' == $source && !empty($_SERVER['HTTP_COOKIE'])) {

            // $_SERVER['HTTP_COOKIE'] may contain duplicated xid values when X-Cart is installed in the subdirectory
            // of other X-Cart installation (see BUG-2983)
            // We need to try each xid to detect which is correct...
            foreach (explode(';', $_SERVER['HTTP_COOKIE']) as $elem) {
                @list($name, $value) = explode('=', $elem);
                if (trim($name) == $arg) {
                    $session = $this->loadSession(trim($value));
                    if ($session) {
                        $sid = trim($value);
                        break;
                    }
                }
            }
        }

        if (!$session) {
            $session = $this->loadSession($sid);
        }

        // If the $session is null and $source is not a cookie
        //      for example:
        //      $sid is from GET but it is expired right now
        // Then $sid is verified from COOKIE (the user can be logged in already and cookie stores this auth info)
        if (!$session && 'COOKIE' !== $source && isset($GLOBALS['_COOKIE'][$arg])) {
            $sid    = $GLOBALS['_COOKIE'][$arg];
            $source = 'COOKIE';

            $session = $this->loadSession($sid);
        }

        return array($session, $source);
    }

    /**
     * Create session
     *
     * @return void
     */
    protected function createSession()
    {
        if ($this->useDumpSession() || $this->isXidFreePaymentReturn()) {
            $this->session = new \XLite\Model\SessionDump();

        } else {
            $this->session = $this->createNewSession();

            \XLite\Core\Database::getEM()->persist($this->session);
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Set cookie
     *
     * @return void
     */
    protected function setCookie()
    {
        if (
            'cli' !== PHP_SAPI
            && !headers_sent()
            && (
                \XLite\Core\Request::getInstance()->isHTTPS()
                || !\XLite\Core\Config::getInstance()->Security->customer_security
            )
        ) {
            if (!$this->isDump()) {
                \XLite\Core\Request::getInstance()->setCookie(
                    $this->getName(),
                    $this->getID(),
                    \XLite\Model\Session::getMaxTTL()
                );

                $this->setLCRefererCookie();
            }
        }
    }

    /**
     * Set referer cookie (this is stored when user register new profile)
     *
     * @return void
     */
    protected function setLCRefererCookie()
    {
        if (!isset($_COOKIE[static::LC_REFERER_COOKIE_NAME]) && isset($_SERVER['HTTP_REFERER'])) {

            $referer = parse_url($_SERVER['HTTP_REFERER']);

            if (isset($referer['host']) && $referer['host'] != $_SERVER['HTTP_HOST']) {
                \XLite\Core\Request::getInstance()->setCookie(
                    static::LC_REFERER_COOKIE_NAME,
                    $_SERVER['HTTP_REFERER'],
                    $this->getLCRefererCookieTTL()
                );
            }
        }
    }

    /**
     * Get parsed URL for Set-Cookie
     *
     * @param boolean $secure Secure protocol or not OPTIONAL
     *
     * @return array
     */
    protected function getCookieURL($secure = false)
    {
        $url = $secure
            ? 'https://' .  \XLite::getInstance()->getOptions(array('host_details', 'https_host'))
            : 'http://' . \XLite::getInstance()->getOptions(array('host_details', 'http_host'));

        $url .= \XLite::getInstance()->getOptions(array('host_details', 'web_dir'));

        return parse_url($url);
    }

    /**
     * Get host / domain for Set-Cookie
     *
     * @param boolean $secure Secure protocol or not OPTIONAL
     *
     * @return string
     */
    protected function getCookieDomain($secure = false)
    {
        $url = $this->getCookieURL($secure);

        return false === strstr($url['host'], '.') ? false : $url['host'];
    }

    /**
     * Get URL path for Set-Cookie
     *
     * @param boolean $secure Secure protocol or not OPTIONAL
     *
     * @return string
     */
    protected function getCookiePath($secure = false)
    {
        $url = $this->getCookieURL($secure);

        return isset($url['path']) ? $url['path'] : '/';
    }

    /**
     * Get referer cookie TTL (seconds)
     *
     * @return integer
     */
    protected function getLCRefererCookieTTL()
    {
        return 3600 * 24 * 180; // TTL is 180 days
    }

    /**
     * Get current language
     *
     * @param string $zone Store zone OPTIONAL
     * @return string Language code
     */
    public function getCurrentLanguage($zone = null)
    {
        $code = $this->__get('language');
        if (!$zone) {
            $zone = \XLite::isAdminZone() ? 'admin' : 'customer';
        }

        if (!is_array($code)) {
            $code = array();
        }

        $useCleanUrls = defined('LC_USE_CLEAN_URLS') && true == LC_USE_CLEAN_URLS;

        $languageCodeFromRequest = \XLite\Core\Request::getInstance()->getLanguageCode();

        if ($useCleanUrls && \XLite\Core\Router::getInstance()->isUseLanguageUrls() && $languageCodeFromRequest) {
            $code = array_merge($code, ['customer' => $languageCodeFromRequest]);
        }

        if (!empty($code[$zone])) {
            $language = \XLite\Core\Database::getRepo('XLite\Model\Language')->findOneByCode($code[$zone]);

            if (!isset($language) || !$language->getAdded() || !$language->getEnabled()) {
                unset($code[$zone]);
            } elseif ($useCleanUrls && \XLite\Core\Router::getInstance()->isUseLanguageUrls() && $languageCodeFromRequest) {
                $lang = $this->__get('language') ?: [];
                $lang['customer'] = $languageCodeFromRequest;
                $this->__set('language', $lang);
            }
        }

        if (empty($code[$zone])) {
            $this->setLanguage($this->defineCurrentLanguage());
            $code = $this->__get('language');
        }

        return $code[$zone];
    }

    /**
     * Define current language
     *
     * @return string Language code
     */
    protected function defineCurrentLanguage()
    {
        $languages = \XLite\Core\Database::getRepo('XLite\Model\Language')->findActiveLanguages();
        if (!\XLite::isAdminZone() && !empty($languages)) {
            $language = isset(\XLite\Core\Config::getInstance()->General)
                ? \XLite\Core\Config::getInstance()->General->default_language
                : 'en';

            $result = \Includes\Utils\ArrayManager::searchInObjectsArray(
                $languages,
                'getCode',
                $language
            );
        }

        return isset($result) ? $result->getCode() : static::getDefaultLanguage();
    }

    /**
     * Use dump session or not
     *
     * @return boolean
     */
    protected function useDumpSession()
    {
        return \XLite\Core\Request::getInstance()->isBot();
    }

    /**
     * Check if payment return request without xid (it is possible in case lax/strict xid cookie)
     *
     * @return bool
     */
    protected function isXidFreePaymentReturn()
    {
        [$session,] = $this->detectPublicSession();

        $target = \XLite\Core\Request::getInstance()->target;

        return !$session && \XLite\Core\Converter::convertFromCamelCase($target) === 'payment_return';
    }

    /**
     * Load session
     *
     * @param string $sid Session id
     *
     * @return \XLite\Model\Session
     */
    protected function loadSession($sid)
    {
        if ($this->useDumpSession()) {
            $session = new \XLite\Model\SessionDump;

        } else {
            $session = \XLite\Core\Database::getRepo('XLite\Model\Session')->findOneBySid($sid);
        }

        return $session;
    }

    // {{{ Cron tasks

    /**
     * Run cron tasks
     *
     * @return void
     */
    protected function runCronTasks()
    {
        if ($this->isCronActive()) {
            foreach ($this->getCronTasks() as $method) {
                $this->$method();
            }
        }
    }

    /**
     * Return true if cron tasks should be run
     *
     * @return boolean
     */
    protected function isCronActive()
    {
        // Run cron tasks once per 100 sessions
        return !\XLite\Core\Request::getInstance()->isCLI()
            && \XLite\Core\Config::getInstance()->General
            && \XLite\Core\Config::getInstance()->General->internal_cron_enabled
            && null !== $this->session
            && 0 === $this->session->getId() % 100;
    }

    /**
     * Get list of cron tasks
     *
     * @return array
     */
    protected function getCronTasks()
    {
        return array(
            'runGarbageCollectOrders',
            'clearGarbage',
        );
    }

    /**
     * Run cron task Garbage collect orders
     *
     * @return array
     */
    protected function runGarbageCollectOrders()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Order')->collectGarbage();
    }

    // }}}

    /**
     * Return array of \XLite\Model\AccessControlCell belongs to this session
     *
     * @return \XLite\Model\AccessControlCell[]
     */
    public function getAccessControlCells()
    {
        $cells = [];
        $hashes = (array)$this->access_control_cells;

        if (!empty($hashes)) {
            $cells = \XLite\Core\Database::getRepo('\XLite\Model\AccessControlCell')->findByHashes($hashes);

            foreach ($cells as $key => $cell) {
                if (!is_object($cell)) {
                    unset($cells[$key]);
                }
            }
        }

        return $cells;
    }

    /**
     * @param string $hash
     *
     * @return $this
     */
    public function addAccessControlCellHash($hash)
    {
        $hashes = (array)$this->access_control_cells;
        $hashes[] = $hash;
        $this->access_control_cells = array_unique($hashes);

        return $this;
    }

    /**
     * @return mixed|string
     */
    public function getSecondDomainSessionId()
    {
        if ($this->secondDomainSessionId !== null) {
            return $this->secondDomainSessionId;
        }

        $request    = Request::getInstance();
        $cookieName = '_' . static::ARGUMENT_NAME;
        $sessionId  = $request->getCookieData()[$cookieName] ?? null;

        if ($sessionId) {
            $session = $this->loadSession($sessionId);
            if (!$session || ($session->getSid() && $session->getExpiry() >= \XLite\Core\Converter::time())) {
                $sessionId = null;
            }
        }

        if (empty($sessionId)) {
            $currentSession = $this->getModel();
            $session        = $this->createNewSession();
            $sessionId      = $session->getSid();

            foreach ($currentSession->getCellsCache() as $cell) {
                $cell->detach();
                $cell->setSession($session);
                $session->addCells($cell);
                \XLite\Core\Database::getEM()->persist($cell);
            }

            \XLite\Core\Database::getEM()->flush();

            $request->setCookie($cookieName, $sessionId);
        }

        $this->secondDomainSessionId = $sessionId;

        return $this->secondDomainSessionId;
    }
}
