<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Settings
 * todo: FULL REFACTOR!!!
 */
class Settings extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Values to use for $page identification
     */
    const GENERAL_PAGE      = 'General';
    const COMPANY_PAGE      = 'Company';
    const EMAIL_PAGE        = 'Email';
    const ENVIRONMENT_PAGE  = 'Environment';
    const PERFORMANCE_PAGE  = 'Performance';
    const UNITS_PAGE        = 'Units';
    const LAYOUT_PAGE       = 'Layout';
    const CLEAN_URL         = 'CleanURL';

    /**
     * Params
     *
     * @var array
     */
    protected $params = array('target', 'page');

    /**
     * Page
     *
     * @var string
     */
    public $page = self::GENERAL_PAGE;

    /**
     * _waiting_list
     * @todo: rename
     *
     * @var mixed
     */
    public $_waiting_list;

    /**
     * Curl response temp variable
     *
     * @var mixed
     */
    private $curlResponse;

    /**
     * @var array
     */
    protected $requirements;

    /**
     * Define body classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    public function defineBodyClasses(array $classes)
    {
        $classes = parent::defineBodyClasses($classes);

        $list = $this->getPages();
        if (isset($list[$this->page])) {
            $classes[] = 'settings-'
                . str_replace('_', '-', \XLite\Core\Converter::convertFromCamelCase(preg_replace('/\W/', '', $list[$this->page])));
        }

        return $classes;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $list = $this->getPages();

        return isset($list[$this->page])
            ? $list[$this->page]
            : '';
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        $list = $this->getPages();

        /**
         * Settings controller is available directly if the $page request variable is provided
         * if the $page is omitted, the controller must be the subclass of Settings main one.
         *
         * The inner $page variable must be in the getPages() array
         */
        return parent::checkAccess()
            && isset($list[$this->page])
            && (
                ($this instanceof \XLite\Controller\Admin\Settings && isset(\XLite\Core\Request::getInstance()->page))
                || is_subclass_of($this, '\XLite\Controller\Admin\Settings')
            );
    }

    // {{{ Pages

    /**
     * Get tab names
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        $list[static::GENERAL_PAGE]     = static::t('Cart & checkout');
        $list[static::COMPANY_PAGE]     = static::t('Store info');
        $list[static::EMAIL_PAGE]       = static::t('Email settings');
        $list[static::ENVIRONMENT_PAGE] = static::t('Environment');
        $list[static::CLEAN_URL]        = static::t('SEO settings');

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();

        foreach ($this->getPages() as $name => $title) {
            $list[$name] = 'settings/base.twig';
        }

        $list[static::ENVIRONMENT_PAGE] = 'settings/summary/body.twig';
        $list[static::CLEAN_URL] = 'settings/clean_url/tabs.twig';

        return $list;
    }

    /**
     * @return array
     */
    public function getCleanUrlCommentedData()
    {
        $result = [];

        if (\XLite\Core\Request::getInstance()->page == 'CleanURL') {
            $result = [
                'companyName'               => \XLite\Core\Config::getInstance()->Company->company_name,
                'companyNameLabel'          => static::t('Company name'),
                'delimiter'                 => " > ",
                'productTitle'              => static::t('Product'),
                'categoryTitle'             => static::t('Category'),
                'staticTitle'               => static::t('Page'),
                'categoryNameLabel'         => static::t('Category name'),
                'parentCategoryNameLabel'   => static::t('Parent category name'),
                'productNameLabel'          => static::t('Product name'),
                'staticPageNameLabel'       => static::t('Page name'),
            ];
        }

        return $result;
    }

    // }}}

    // {{{ Other

    /**
     * Get options for current tab (category)
     *
     * @return \XLite\Model\Config[]
     */
    public function getOptions()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Config')->findByCategoryAndVisible($this->page);
    }

    /**
     * getModelFormClass
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\Settings';
    }

    // }}}

    // {{{ Additional methods

    /**
     * Defines if the clean URL is enabled in the store
     *
     * @return boolean
     */
    public function isCleanURLEnabled()
    {
        return LC_USE_CLEAN_URLS;
    }

    /**
     * Defines if the clean urls can be enabled on the current server environment
     *
     * @return boolean
     */
    public function canEnableCleanURL()
    {
        $urlToCheck = \XLite::getInstance()->getShopURL() . \XLite::CLEAN_URL_CHECK_QUERY;
        $request = new \XLite\Core\HTTP\Request($urlToCheck);
        $request->setAdditionalOption(\CURLOPT_SSL_VERIFYPEER, false);
        $request->setAdditionalOption(\CURLOPT_SSL_VERIFYHOST, false);
        $this->curlResponse = $request->sendRequest();

        return !$this->isCleanURLEnabled()
            && $this->curlResponse
            && in_array($this->curlResponse->code, array(200, 301, 302));
    }

    /**
     * Defines the article URL of setting up the clean URL functionality
     *
     * @return string
     */
    public function getCleanURLArticleURL()
    {
        return static::t('https://kb.x-cart.com/seo_and_promotion/setting_up_seo-friendly_urls.html');
    }

    /**
     * Defines the article URL of setting up the clean URL functionality
     *
     * @return string
     */
    public function getInstallationDirectoryHelpLink()
    {
        return static::t('https://kb.x-cart.com/general_setup/moving_x-cart_to_another_location.html');
    }
    /**
     * Check for the GDLib extension
     *
     * @return boolean
     */
    public function isGDLibLoaded()
    {
        return extension_loaded('gd') && function_exists('gd_info');
    }

    /**
     * isOpenBasedirRestriction
     *
     * @return boolean
     */
    public function isOpenBasedirRestriction()
    {
        $res = (string) @ini_get('open_basedir');

        return ('' !== $res);
    }

    /**
     * Get translation driver identifier
     *
     * @return string
     */
    public function getTranslationDriver()
    {
        return \XLite\Core\Translation::getInstance()->getDriver()->getName();
    }

    /**
     * Get translation driver identifier
     *
     * @return string
     */
    public function getServerDateTime()
    {
        $time = new \DateTime('now');

        return $time->format('c');
    }

    /**
     * Get translation driver identifier
     *
     * @return string
     */
    public function getServerTimezone()
    {
        $time = new \DateTime('now');

        return $time->getTimezone()->getName();
    }

    /**
     * Get translation driver identifier
     *
     * @return string
     */
    public function getShopDateTime()
    {
        $time = new \DateTime('now', \XLite\Core\Converter::getTimeZone());

        return $time->format('c');
    }

    /**
     * Get translation driver identifier
     *
     * @return string
     */
    public function getShopTimezone()
    {
        $time = new \DateTime('now', \XLite\Core\Converter::getTimeZone());

        return $time->getTimezone()->getName();
    }

    /**
     * Returns value by request
     *
     * @param string $name Type of value
     *
     * @return string
     */
    public function get($name)
    {
        switch($name) {

            case 'phpversion':
                $return = PHP_VERSION;
                break;

            case 'os_type':
                list($osType) = explode(' ', PHP_OS);
                $return = $osType;
                break;

            case 'mysql_server':
                $return = \Includes\Utils\Database::getDbVersion();
                break;

            case 'innodb_support':
                $return = \Includes\Utils\Database::isInnoDBSupported();
                break;

            case 'root_folder':
                $return = getcwd();
                break;

            case 'web_server':
                $return = isset($_SERVER['SERVER_SOFTWARE'])
                    ? $_SERVER['SERVER_SOFTWARE']
                    : '';
                break;

            case 'xml_parser':
                $return = $this->getXMLParserValue();
                break;

            case 'gdlib':
                $return = $this->getGdlibValue();
                break;

            case 'core_version':
                $return = \XLite::getInstance()->getVersion();
                break;

            case 'libcurl':
                $return = $this->getLibcurlValue();
                break;

            case 'license_keys':
                $return = [];
                break;

            default:
                $return = parent::get($name);
        }

        return $return;
    }

    /**
     * Get XML parser value
     *
     * @return string
     */
    public function getXMLParserValue()
    {
        ob_start();
        phpinfo(INFO_MODULES);
        $phpInfo = ob_get_contents();
        ob_end_clean();

        $return = null;
        if (preg_match('/EXPAT.+>([\.\d]+)/mi', $phpInfo, $m)) {
            $return = $m[1];
        } else {
            $return = function_exists('xml_parser_create') ? 'found' : '';
        }

        return $return;
    }

    /**
     * Get Gdlib value
     *
     * @return string
     */
    public function getGdlibValue()
    {
        $return = null;

        if (!$this->is('GDLibLoaded')) {
            $return = '';

        } else {
            ob_start();

            phpinfo(INFO_MODULES);

            $phpInfo = ob_get_contents();

            ob_end_clean();

            $gdVersion = @gd_info();
            $gdVersion = (is_array($gdVersion) && isset($gdVersion['GD Version']))
                ? $gdVersion['GD Version']
                : null;

            if (!$gdVersion) {
                $isMatched = preg_match('/GD.+>([\.\d]+)/mi', $phpInfo, $m);

                $gdVersion = $isMatched
                    ? $m[1]
                    : 'unknown';

            }

            $return = 'found (' . $gdVersion . ')';
        }

        return $return;
    }

    /**
     * Get Libcurl value
     *
     * @return string
     */
    public function getLibcurlValue()
    {
        $return = null;

        if (function_exists('curl_version')) {
            $libcurlVersion = curl_version();

            if (is_array($libcurlVersion)) {
                $libcurlVersion = $libcurlVersion['version'];
            }

            $return = $libcurlVersion;

        } else {
            $return = false;
        }

        return $return;
    }

    /**
     * Try permissions
     *
     * @param  string   $dir      Dir to create
     * @param  string   $modeStr  Permissions string
     *
     * @return boolean
     */
    public function tryPermissions($dir, $modeStr = null)
    {
        $perm = substr(
            sprintf('%o', @fileperms($dir)),
            -4
        );

        return $modeStr === $perm;
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('phpinfo', 'switch_clean_url'));
    }

    /**
     * doActionPhpinfo
     *
     * @return void
     */
    public function doActionPhpinfo()
    {
        phpinfo();
        $this->setSuppressOutput(true);
    }

    /**
     * doActionUpdate
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $this->getModelForm()->performAction('update');
    }

    /**
     * Get error message header
     *
     * @return string
     */
    protected function getErrorMessageHeader()
    {
        $message = 'Clean_urls_error_message';

        return static::t($message, array('url' => $this->curlResponse->uri));
    }

    /**
     * Get error message by code
     *
     * @param integer $code Code
     *
     * @return string
     */
    protected function getErrorMessageCodeExplanation($code)
    {
        // TODO Add some explanation
        $explanation = '';
        switch ($code) {
            case 500:
                $explanation .= 'Internal server error';
                break;
            case 404:
                $explanation .= 'Page not found';
                break;
        }

        return static::t('Error code explanation:') . ' ' . $code . ' '. $explanation;
    }

    /**
     * Actions to enable the clean URL functionality
     *
     * @return void
     */
    public function doActionSwitchCleanUrl()
    {
        $oldValue = (bool) \XLite\Core\Config::getInstance()->CleanURL->clean_url_flag;
        $ajaxResponse = array(
            'Success'       => true,
            'Error'         => '',
            'NewState'      => !(bool) $oldValue
        );

        if ($oldValue === false && !$this->canEnableCleanURL()) {
            $ajaxResponse['Success'] = false;
            $ajaxResponse['Error'] = array(
                'msg'   => $this->getErrorMessageHeader(),
                'body'  => $this->getErrorMessageCodeExplanation($this->curlResponse->code)
            );

        } else {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                array(
                    'category' => 'CleanURL',
                    'name'     => 'clean_url_flag',
                    'value'    => !(bool) $oldValue
                )
            );
        }

        $this->printAJAX($ajaxResponse);
        $this->silent = true;
        $this->setSuppressOutput(true);
    }

    /**
     * Actions to enable the clean URL functionality
     *
     * @return void
     */
    public function doActionEnableCleanUrl()
    {
        if ($this->canEnableCleanURL()) {
            /** @var \XLite\Model\Config $cleanURLFlag */
            $cleanURLFlag = \XLite\Core\Database::getRepo('XLite\Model\Config')->findOneBy(
                array(
                    'name'      => 'clean_url_flag',
                    'category'  => 'CleanURL'
                )
            );

            \XLite\Core\Database::getRepo('XLite\Model\Config')->update(
                $cleanURLFlag,
                array(
                    'value' => true,
                )
            );

            \XLite\Core\TopMessage::addInfo(static::t('Clean URLs are enabled'));
        }

        $this->doRedirect();
    }

    /**
     * isWin
     *
     * @return boolean
     */
    public function isWin()
    {
        return (LC_OS_CODE === 'win');
    }

    /**
     * getStateById
     *
     * @param mixed $stateId State Id
     *
     * @return \XLite\Model\State
     */
    public function getStateById($stateId)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\State')->find($stateId);
    }

    // }}}

    // {{{ Requirements

    public function getCriticalRequirements()
    {
        $result = [];
        foreach ($this->getRequirements() as $name => $requirement) {
            if ($requirement['level'] === \Includes\Requirements::LEVEL_CRITICAL) {
                $result[$name] = $requirement;
            }
        }

        return $result;
    }

    public function getNonCriticalRequirements()
    {
        $result = [];
        foreach ($this->getRequirements() as $name => $requirement) {
            if ($requirement['level'] !== \Includes\Requirements::LEVEL_CRITICAL) {
                $result[$name] = $requirement;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getRequirements()
    {
        if ($this->requirements === null) {
            $requirements = new \Includes\Requirements();

            $this->requirements = $this->skipCheck()
                ? $this->getSkippedRequirements($requirements)
                : $this->getCheckedRequirements($requirements);
        }

        return $this->requirements;
    }

    /**
     * @return bool
     */
    protected function skipCheck()
    {
        return \XLite\Core\Request::getInstance()->doNotSkipCheck
            ? false
            : true;
    }

    /**
     * @param \Includes\Requirements $requirements
     *
     * @return array
     */
    protected function getSkippedRequirements(\Includes\Requirements $requirements)
    {
        $result = [];

        foreach ($requirements->getUnchecked() as $name => $requirement) {
            $requirement['status'] = true;
            $requirement['title'] = static::t($requirement['title']);
            $requirement['skipped'] = true;

            $requirement['error_description'] = '';

            $result[$name] = $requirement;
        }

        return $result;
    }

    /**
     * @param \Includes\Requirements $requirements
     *
     * @return array
     */
    protected function getCheckedRequirements(\Includes\Requirements $requirements)
    {
        $result = [];

        foreach ($requirements->getResult() as $name => $requirement) {
            $requirement['status'] = $requirement['state'] === \Includes\Requirements::STATE_SUCCESS;
            $requirement['title'] = static::t($requirement['title']);
            $requirement['skipped'] = false;

            $requirement['error_description'] = isset($requirement['description'])
                ? $this->getRequirementTranslation($name . '.' . $requirement['description'], $requirement)
                : '';

            $requirement['description'] = $this->getRequirementTranslation($name . '.label_message', $requirement);
            $requirement['kb_description'] = $this->getRequirementTranslation($name . '.kb_message', $requirement);

            $result[$name] = $requirement;
        }

        return $result;
    }

    protected function getRequirementTranslation($name, $requirement)
    {
        $value = static::t($name, array_filter($requirement['data'], 'is_scalar'));

        return $name === $value ? '' : $value;
    }

    // }}}
}
