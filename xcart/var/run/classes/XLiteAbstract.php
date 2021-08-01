<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

use XLite\Core\Database\Migration\UnsupportedDatabaseOperationDuringMaintenanceException;


/**
 * Application singleton
 *
 *  Swg\Swagger (
 *     schemes={"http"},
 *     host="demostore.x-cart.com",
 *     basePath="/admin.php?target=RESTAPI&",
 *     produces={"application/json", "application/xml"},
 *     consumes={"application/json", "application/x-www-form-urlencoded"},
 *      Swg\Info (
 *         version="5.3.5.5",
 *         title="X-Cart REST API",
 *         description="",
 *     ),
 *      Swg\ExternalDocumentation (
 *         description="Find out more about X-Cart REST API",
 *         url="https://devs.x-cart.com/rest-api/"
 *     )
 * )
 *
 *  SWG\SecurityScheme(
 *   securityDefinition="api_key",
 *   type="apiKey",
 *   in="query",
 *   name="_key"
 * )
 *
 * TODO: to revise
 * TODO[SINGLETON]: lowest priority
 */
abstract class XLiteAbstract extends \XLite\Base
{
    use \XLite\Core\DependencyInjection\ContainerHolderTrait;

    /**
     * Core version
     */
    const XC_VERSION = '5.4.1.23';

    /**
     * Endpoints
     */
    const CART_SELF  = 'cart.php';
    const ADMIN_SELF = 'admin.php';

    /**
     * This target will be used if the "target" params is not passed in the request
     */
    const TARGET_DEFAULT = 'main';
    const TARGET_404     = 'page_not_found';

    /**
     * Interfaces codes
     */
    const ADMIN_INTERFACE    = 'admin';
    const CUSTOMER_INTERFACE = 'customer';
    const CONSOLE_INTERFACE  = 'console';
    const MAIL_INTERFACE     = 'mail';
    const COMMON_INTERFACE   = 'common';
    const PDF_INTERFACE      = 'pdf';

    /**
     * Default shop currency code (840 - US Dollar)
     */
    const SHOP_CURRENCY_DEFAULT = 840;

    /**
     * Temporary variable name for latest cache building time
     */
    const CACHE_TIMESTAMP = 'cache_build_timestamp';

    /**
     * Trial period TTL (days)
     */
    const TRIAL_PERIOD = '30';

    /**
     * Session variable name for show trial notice flag.
     */
    const SHOW_TRIAL_NOTICE = 'showTrialNotice';

    /**
     * Producer site URL
     */
    const PRODUCER_SITE_URL = 'https://www.x-cart.com/';

    /**
     * Name of the form id
     */
    const FORM_ID = 'xcart_form_id';

    /**
     * URI to check clean URLS availability
     */
    const CLEAN_URL_CHECK_QUERY = 'check/for/clean/urls.html';

    /**
     * Parsed version.
     * Array with the following keys: major, minor, build, minorFull
     *
     * @var array
     */
    protected $parsedVersion;

    /**
     * Current area flag
     *
     * @var boolean
     */
    protected static $adminZone = false;

    /**
     * URL type flag
     *
     * @var boolean
     */
    protected static $cleanURL = false;

    /**
     * Called controller
     *
     * @var \XLite\Controller\AController
     */
    protected static $controller;

    /**
     * Flag; determines if we need to cleanup (and, as a result, to rebuild) classes and templates cache
     *
     * @var boolean
     */
    protected static $isNeedToCleanupCache = false;

    /**
     * Current currency
     *
     * @var \XLite\Model\Currency
     */
    protected $currentCurrency;

    /**
     * Check is admin interface
     *
     * @return boolean
     */
    public static function isAdminZone()
    {
        return static::$adminZone;
    }

    /**
     * Return filename of the admin script. Defaults to 'admin.php'.
     *
     * @return string
     */
    public static function getAdminScript()
    {
        return \Includes\Utils\ConfigParser::getOptions(array('host_details', 'admin_self')) ?: static::ADMIN_SELF;
    }

    /**
     * Return filename of the customer script. Defaults to 'cart.php'.
     *
     * @return string
     */
    public static function getCustomerScript()
    {
        return \Includes\Utils\ConfigParser::getOptions(array('host_details', 'cart_self')) ?: static::CART_SELF;
    }

    /**
     * Check is admin interface
     *
     * @return boolean
     */
    public static function isAdminScript()
    {
        $adminScript = \Includes\Utils\FileManager::isFileReadable(static::getAdminScript())
            ? static::getAdminScript()
            : self::ADMIN_SELF;

        return false !== strpos(static::getInstance()->getRequestedScript(), $adminScript);
    }

    /**
     * Check is cache building
     *
     * @return boolean
     */
    public static function isCacheBuilding()
    {
        return defined('LC_CACHE_BUILDING') && constant('LC_CACHE_BUILDING');
    }

    /**
     * Check if clean URL used
     *
     * @return boolean
     */
    public static function isCleanURL()
    {
        return static::$cleanURL;
    }

    /**
     * Get cache cleanup flag state
     *
     * @return boolean
     */
    public static function getCleanUpCacheFlag()
    {
        return static::$isNeedToCleanupCache;
    }

    /**
     * Ability to provoke cache cleanup (or to prevent it)
     *
     * @param boolean $flag If it's needed to cleanup cache or not
     *
     * @return void
     */
    public static function setCleanUpCacheFlag($flag)
    {
        static::$isNeedToCleanupCache = (true === $flag);
        if (static::$isNeedToCleanupCache) {
            \Includes\Decorator\Utils\CacheManager::setCacheRebuildMark();

        } else {
            \Includes\Decorator\Utils\CacheManager::unsetCacheRebuildMark();
        }
    }

    /**
     * Get controller
     *
     * @return \XLite\Controller\AController
     */
    public static function getController()
    {
        if (null === static::$controller) {
            $class = static::getControllerClass();

            // If mod_rewrite is disabled and ErrorDocument 404 is set
            //  on check/for/clean/urls.html uri we are trying do display 404
            // This is here to speedup check request
            if (\XLite\Core\Request::getInstance()->target === static::TARGET_404
                && isset($_SERVER['REQUEST_URI'])
                && strpos($_SERVER['REQUEST_URI'], static::CLEAN_URL_CHECK_QUERY) !== false
            ) {
                http_response_code(404);
                die();
            };

            if (!$class) {
                \XLite\Core\Request::getInstance()->target = static::TARGET_404;
                $class = static::getControllerClass();
            }

            if (!\XLite\Core\Request::getInstance()->isCLI()
                && \XLite::getInstance()->getRequestedScript() !== \XLite::getInstance()->getExpectedScript()
                && \XLite::getInstance()->getRequestedScript() !== \XLite::getInstance()->getExpectedScript(true)
            ) {
                \XLite\Core\Request::getInstance()->target = static::TARGET_404;
                $class = static::getControllerClass();
            }

            static::$controller = new $class(\XLite\Core\Request::getInstance()->getData());

            if (
                static::isAdminZone()
                && 'keys_notice' != \XLite\Core\Request::getInstance()->target
                && static::$controller->isDisplayBlockContent()
                && static::$controller->isBlockContentAllowed()
            ) {
                \XLite\Core\Request::getInstance()->target = 'keys_notice';
                static::$controller = new \XLite\Controller\Admin\KeysNotice(\XLite\Core\Request::getInstance()->getData());
            }

            static::$controller->init();
        }

        return static::$controller;
    }

    /**
     * Set controller
     * FIXME - to delete
     *
     * @param mixed $controller Controller OPTIONAL
     *
     * @return void
     */
    public static function setController($controller = null)
    {
        if ($controller instanceof \XLite\Controller\AController || null === $controller) {
            static::$controller = $controller;
        }
    }

    /**
     * Defines the installation language code
     *
     * @return string
     */
    public static function getInstallationLng()
    {
        return \Includes\Utils\ConfigParser::getInstallationLng();
    }

    /**
     * Return X-Cart 5 license of the core
     *
     * @param boolean $force Flag: true - ignore runtime cache
     *
     * @return array
     */
    public static function getXCNLicense()
    {
        return \XLite\Core\Marketplace::getInstance()->getCoreLicense();
    }

    /**
     * Return X-Cart 5 license of the core
     *
     * @return string
     */
    public static function getXCNLicenseKey()
    {
        $license = static::getXCNLicense();

        return $license ? $license['keyValue'] : '';
    }

    /**
     * Return if X-Cart 5 has license of the core
     *
     * @return boolean
     */
    public static function hasXCNLicenseKey()
    {
        return (bool) static::getXCNLicense();
    }

    /**
     * @return int
     */
    public static function getInstallationTimestamp()
    {
        $result = 0;
        $installationData = \XLite\Core\Marketplace::getInstance()->getInstallationData();

        if (!empty($installationData['installationDate'])) {
            $result = (int) $installationData['installationDate'];
        }

        return $result;
    }

    /**
     * Get number of days left before trial period will expire
     *
     * @param boolean $returnDays Flag: return in days
     *
     * @return integer
     */
    public static function getTrialPeriodLeft($returnDays = true)
    {
        $startTime = \XLite::getInstallationTimestamp();
        $endTime   = $startTime + 86400 * \XLite::TRIAL_PERIOD;

        return $returnDays
            ? ceil(($endTime - time()) / 86400)
            : $endTime - time();
    }

    /**
     * Check if trial period is expired
     *
     * @return boolean
     */
    public static function isTrialPeriodExpired()
    {
        return !static::hasXCNLicenseKey() && 0 >= static::getTrialPeriodLeft(false);
    }

    /**
     * Return true if registered Free license
     *
     * @return boolean
     */
    public static function isFreeLicense()
    {
        $result = false;
        $key = static::getXCNLicense();

        if ($key) {
            if (2 === (int) ($key['xcnPlan'] ?? 0)) {
                $result = true;

            } else {
                $keyData = $key['keyData'];
                $result = isset($keyData['editionName']) && 'Free' === $keyData['editionName'];
            }
        }

        return $result;
    }

    /**
     * Return affiliate ID
     *
     * @return string
     */
    public static function getAffiliateId()
    {
        return \Includes\Utils\ConfigParser::getOptions(array('affiliate', 'id'));
    }

    /**
     * Return affiliate URL
     *
     * @param string  $url                Url part to add OPTIONAL
     * @param boolean $useInstallationLng Use installation language or not OPTIONAL
     *
     * @return string
     */
    public static function getXCartURL($url = '', $useInstallationLng = true)
    {
        $controllerTarget = '';
        if (static::isAdminZone() && static::getController()) {
            $controllerTarget = static::getController()->getTarget();
        }

        return \Includes\Utils\URLManager::getAffiliatedXCartURL(
            $url,
            $controllerTarget,
            static::getAffiliateId(),
            static::getInstallationLng(),
            $useInstallationLng
        );
    }

    /**
     * Return current target
     *
     * @return string
     */
    protected static function getTarget()
    {
        if (empty(\XLite\Core\Request::getInstance()->target)) {
            \XLite\Core\Request::getInstance()->target = static::dispatchRequest();
        }

        return \XLite\Core\Request::getInstance()->target;
    }

    /**
     * Assemble and get controller class name
     *
     * @return string
     */
    protected static function getControllerClass()
    {
        return \XLite\Core\Converter::getControllerClass(static::getTarget());
    }

    /**
     * Return specified (or the whole list) options
     *
     * @param mixed $names List (or single value) of option names OPTIONAL
     *
     * @return mixed
     */
    public function getOptions($names = null)
    {
        return \XLite\Core\ConfigParser::getOptions($names);
    }

    /**
     * Return current endpoint script
     *
     * @param boolean $check Check if file exists and readable (default: false)
     *s
     * @return string
     */
    public function getScript($check = false)
    {
        return static::isAdminZone()
             ? ( !$check || \Includes\Utils\FileManager::isFileReadable(static::getAdminScript()) ? static::getAdminScript() : self::ADMIN_SELF )
             : ( !$check || \Includes\Utils\FileManager::isFileReadable(static::getCustomerScript()) ? static::getCustomerScript() : self::CART_SELF );
    }

    /**
     * Return current endpoint script
     *
     * @return string
     */
    protected function getRequestedScript()
    {
        return trim($_SERVER['PHP_SELF'], '/');
    }

    /**
     * Return current endpoint script
     *
     * @param boolean $index Get index script
     *
     * @return string
     */
    protected function getExpectedScript($index = false)
    {
        $web_dir = rtrim(\XLite::getInstance()->getOptions(array('host_details', 'web_dir')), '/');
        $script = $index
            ? 'index.php'
            : ltrim(static::getScript(true), '/');

        return trim($web_dir . '/' . $script, '/');
    }

    /**
     * Return full URL for the resource
     *
     * @param string  $url      Url part to add OPTIONAL
     * @param boolean $isSecure Use HTTP or HTTPS OPTIONAL
     * @param array   $params   Optional URL params OPTIONAL
     *
     * @return string
     */
    public function getShopURL($url = '', $isSecure = null, array $params = array())
    {
        return \XLite\Core\URLManager::getShopURL($url, $isSecure, $params);
    }

    /**
     * @param string $path
     * @param null   $isSecure
     * @param array  $params
     *
     * @return string
     */
    public function getServiceURL($path = '', $isSecure = null, array $params = array())
    {
        if (!$path) {
            $path = '#/';
        }

        if (strpos($path, '#') === 0) {
            $path = 'service.php?locale=' . \XLite\Core\Session::getInstance()->getCurrentLanguage() . $path . '?';
        }

        return \XLite\Core\URLManager::getShopURL($path, $isSecure, $params);
    }

    /**
     * Return instance of the abstract factory singleton
     *
     * @return \XLite\Model\Factory
     */
    public function getFactory()
    {
        return \XLite\Model\Factory::getInstance();
    }

    /**
     * Call application die (general routine)
     *
     * @param string $message Error message
     *
     * @return void
     */
    public function doGlobalDie($message)
    {
        $this->doDie($message);
    }

    /**
     * Initialize all active modules
     *
     * @return void
     */
    public function initModules()
    {
        //static::checkUpgradeStatus();

        \Includes\Utils\Module\Manager::initModules();
    }

    /**
     * Set up flag if upgrade is in progress.
     * Modules will not be disabled during initialization if this flag is set.
     * @deprecated
     */
    protected static function checkUpgradeStatus()
    {
        $actions = array('pre_upgrade_hooks', 'update_files');

        if (
            !defined('XC_UPGRADE_IN_PROGRESS')
            && 'upgrade' == \XLite\Core\Request::getInstance()->target
            && !empty(\XLite\Core\Request::getInstance()->action)
            && in_array(\XLite\Core\Request::getInstance()->action, $actions)
        ) {
            define('XC_UPGRADE_IN_PROGRESS', true);
        }
    }

    /**
     * Perform an action and redirect
     *
     * @return void
     */
    public function runController()
    {
        static::getController()->handleRequest();
    }

    /**
     * Return viewer object
     *
     * @return \XLite\View\Controller|void
     */
    public function getViewer()
    {
        $this->runController();

        $viewer = static::getController()->getViewer();
        $viewer->init();

        return $viewer;
    }

    /**
     * Process request
     *
     * @return \XLite
     */
    public function processRequest()
    {
        if (!static::isAdminZone()) {
            \XLite\Core\Router::getInstance()->processCleanUrls();
        }

        $this->runController();

        static::getController()->processRequest();

        $this->runPostRequestActions();

        return $this;
    }

    /**
     * Run customer zone application
     */
    public function runCustomerZone()
    {
        $level = ob_get_level();

        try {

            $this->run()->processRequest();

        } catch (UnsupportedDatabaseOperationDuringMaintenanceException $e) {

            // Get back to original output buffering level to discard all buffered content
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            \Includes\Decorator\Utils\CacheManager::triggerMaintenanceModeError();
        }
    }

    /**
     * Run application
     *
     * @param boolean $adminZone Admin interface flag OPTIONAL
     *
     * @return \XLite
     */
    public function run($adminZone = false)
    {
        // Set current area
        static::$adminZone = (bool)$adminZone;

        // Clear some data
        static::clearDataOnStartup();

        // Initialize logger
        \XLite\Logger::getInstance();

        // Initialize modules
        $this->initModules();

        if (\XLite\Core\Request::getInstance()->isCLI()) {
            // Set skin for console interface
            \XLite\Core\Layout::getInstance()->setConsoleSkin();

        } elseif (true === static::$adminZone) {
            // Set skin for admin interface
            \XLite\Core\Layout::getInstance()->setAdminSkin();
        }

        return $this;
    }

    /**
     * Get current currency
     *
     * @return \XLite\Model\Currency
     */
    public function getCurrency()
    {
        if (null === $this->currentCurrency) {
            $this->currentCurrency = \XLite\Core\Database::getRepo('XLite\Model\Currency')
                ->find(\XLite\Core\Config::getInstance()->General->shop_currency ?: static::SHOP_CURRENCY_DEFAULT);
        }

        return $this->currentCurrency;
    }

    /**
     * Return current action
     *
     * @return mixed
     */
    protected function getAction()
    {
        return \XLite\Core\Request::getInstance()->action;
    }

    /**
     * Clear some data
     *
     * @return void
     */
    protected function clearDataOnStartup()
    {
        static::$controller = null;
        \XLite\Model\CachingFactory::clearCache();
    }

    // {{{ Clean URLs support

    /**
     * Dispatch request
     *
     * @return string
     */
    protected static function dispatchRequest()
    {
        $result = static::TARGET_DEFAULT;

        if (isset(\XLite\Core\Request::getInstance()->url)) {
            if (static::isCheckForCleanURL()) {
                $result = null;
                // Request to detect support of clean URLs
                // Just display 'OK' and exit to speedup this checking
                die('OK');
            };

            if (LC_USE_CLEAN_URLS) {
                // Get target
                $result = static::getTargetByCleanURL();

            } else {
                $result = static::TARGET_404;
            }
        }

        return $result;
    }

    /**
     * Return target by clean URL
     *
     * @return string
     */
    protected static function getTargetByCleanURL()
    {
        $tmp = \XLite\Core\Request::getInstance();
        list($target, $params) = \XLite\Core\Converter::parseCleanUrl($tmp->url, $tmp->last, $tmp->rest, $tmp->ext);

        if ($target && $params) {
            $redirectUrl = \XLite\Core\Database::getRepo('XLite\Model\CleanURL')->buildURL($target, $params);
            $redirectUrl = strtok($redirectUrl,'?');
            $web_dir = rtrim(\XLite::getInstance()->getOptions(array('host_details', 'web_dir')), '/');
            $selfURI = substr(strtok(\Includes\Utils\URLManager::getSelfURI(),'?'), strlen($web_dir) + 1);

            if (LC_USE_CLEAN_URLS && \XLite\Core\Router::getInstance()->isUseLanguageUrls()) {
                $language = \XLite\Core\Session::getInstance()->getLanguage();

                $selfURI = strpos($selfURI, $language->getCode() . '/') === 0
                    ? substr($selfURI, 3)
                    : $selfURI;
            }

            if ($redirectUrl !== $selfURI && !\XLite\Core\Request::getInstance()->isAJAX()) {
                $ttl = 86400;
                $expiresTime = gmdate('D, d M Y H:i:s', time() + $ttl) . ' GMT';

                header("Cache-Control: max-age=$ttl, must-revalidate");
                header("Expires: $expiresTime");

                \XLite\Core\Operator::redirect(
                    \XLite\Core\URLManager::getShopURL($redirectUrl),
                    false,
                    301
                );
            }
        }

        if (!empty($target)) {
            $tmp->mapRequest($params);

            static::$cleanURL = true;
        }

        return $target;
    }

    /**
     * Return true if check for clean URLs availability was requested
     *
     * @return boolean
     */
    protected static function isCheckForCleanURL()
    {
        $tmp = \XLite\Core\Request::getInstance();
        $parts = [$tmp->rest, $tmp->last, $tmp->url . $tmp->ext];
        $query = implode('/', array_filter($parts));

        return static::CLEAN_URL_CHECK_QUERY == $query;
    }

    // }}}

    // {{{ Form Id

    /**
     * Create the form id for the widgets
     *
     * @param boolean $createNewFormId Flag: create new form id
     *
     * @return string
     */
    final public static function getFormId($createNewFormId = true)
    {
        $formIdStrategy = \XLite::getInstance()->getFormIdStrategy();

        return $formIdStrategy === 'per-session'
            ? \XLite\Core\Session::getInstance()->createFormId(false)
            : \XLite\Core\Session::getInstance()->createFormId($createNewFormId);
    }

    /**
     * Get formId strategy
     *
     * @return string
     */
    public function getFormIdStrategy()
    {
        return \XLite::getInstance()->getOptions(array('other', 'csrf_strategy'));
    }

    // }}}

    // {{{ Application versions

    final protected function getParsedVersion($partName = null)
    {
        if (!isset($this->parsedVersion)) {
            $version = explode('.', $this->getVersion());
            $this->parsedVersion = array(
                'major' => $version[0] . '.' . $version[1],
                'minor' => !empty($version[2]) ? $version[2] : '0',
                'build' => !empty($version[3]) ? $version[3] : '0',
            );
            $this->parsedVersion['minorFull'] = $this->parsedVersion['minor']
                . ($this->parsedVersion['build'] ? '.' . $this->parsedVersion['build'] : '');
            $this->parsedVersion['hotfix'] = $this->parsedVersion['major'] . '.' . $this->parsedVersion['minor'];
        }

        return !is_null($partName) ? $this->parsedVersion[$partName] : $this->parsedVersion;
    }

    /**
     * Get application version
     *
     * @return string
     */
    final public function getVersion()
    {
        return static::XC_VERSION;
    }

    /**
     * Get application major version (X.X.x.x)
     *
     * @return string
     */
    final public function getMajorVersion()
    {
        return $this->getParsedVersion('major');
    }

    /**
     * Get application minor version (x.x.X.X)
     *
     * @return string
     */
    final public function getMinorVersion()
    {
        return $this->getParsedVersion('minorFull');
    }

    /**
     * Get application version build number (x.x.x.X)
     *
     * @return string
     */
    final public function getBuildVersion()
    {
        return $this->getParsedVersion('build');
    }

    /**
     * Get application minor version (x.x.X.x)
     *
     * @return string
     */
    final public function getMinorOnlyVersion()
    {
        return $this->getParsedVersion('minor');
    }

    /**
     * Get application hot-fixes branch version (X.X.X.x)
     *
     * @return string
     */
    final public function getHotfixBranchVersion()
    {
        return $this->getParsedVersion('hotfix');
    }

    /**
     * Compare a version with the major core version
     *
     * @param string $version  Version to compare
     * @param string $operator Comparison operator
     *
     * @return boolean
     */
    final public function checkVersion($version, $operator)
    {
        return version_compare($this->getMajorVersion(), $version, $operator);
    }

    /**
     * Compare a version with the minor core version
     *
     * @param string $version  Version to compare
     * @param string $operator Comparison operator
     *
     * @return boolean
     */
    final public function checkMinorVersion($version, $operator)
    {
        return version_compare($this->getMinorVersion(), $version, $operator);
    }

    /**
     * Get last cache rebuild time
     *
     * @return integer Timestamp
     */
    public static function getLastRebuildTimestamp()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar(\XLite::CACHE_TIMESTAMP);
    }

    /**
     * @since 5.3.3.2 First appearance
     *
     * Actions to be executed after ignore_user_abort call
     */
    public function runPostRequestActions()
    {
        flush();
        ignore_user_abort(true);

        while (\XLite\Core\Job\InMemoryJobRegistry::getInstance()->hasJobs()) {
            $job = \XLite\Core\Job\InMemoryJobRegistry::getInstance()->consume();
            $job->handle();
        }
    }

    // }}}
}
