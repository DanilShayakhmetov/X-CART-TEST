<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;


/**
 * Abstract admin-zone controller
 */
abstract class AAdminAbstract extends \XLite\Controller\AController
{
    // {{{ TODO Uncomment after traits + decorator fixed
    // And remove getItemsListClass and doActionUpdateItemsList methods

    use \XLite\Controller\Features\ItemsListControllerTrait;

    /**
     * Parameter name
     */
    const PARAM_SEARCH_FILTER_ID = 'searchFilterId';

    /**
     * List of recently logged in administrators
     *
     * @var array
     */
    protected $recentAdmins;

    /**
     * Breadcrumbs
     *
     * @var \XLite\View\Location\Node[]
     */
    protected $locationPath;

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return ((parent::checkAccess() && $this->checkACL()) || $this->isPublicZone())
            && $this->checkFormId();
    }

    /**
     * Set if the form id is needed to make an actions
     * Form class uses this method to check if the form id should be added
     *
     * @return boolean
     */
    public static function needFormId()
    {
        return true;
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(
            parent::defineFreeFormIdActions(),
            array('save_search_filter', 'search')
        );
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }

    /**
     * This function called after template output
     *
     * @return void
     */
    public function postprocess()
    {
        parent::postprocess();

        if ($this->dumpStarted) {
            $this->displayPageFooter();
        }
    }

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return 'access_denied' !== \XLite\Core\Request::getInstance()->target;
    }

    /**
     * Returns 'maintenance_mode' string if frontend is closed or null otherwise
     *
     * @return string
     */
    public function getCustomerZoneWarning()
    {
        return \XLite\Core\Auth::getInstance()->isClosedStorefront() ? 'maintenance_mode' : null;
    }

    /**
     * Get access level
     *
     * @return integer
     */
    public function getAccessLevel()
    {
        return \XLite\Core\Auth::getInstance()->getAdminAccessLevel();
    }

    /**
     * Handles the request to admin interface
     *
     * @return void
     */
    public function handleRequest()
    {
        // Check if user is logged in and has a right access level
        if (!$this->isPublicZone()
            && !\XLite\Core\Auth::getInstance()->isAuthorized($this)
        ) {
            \XLite\Core\Session::getInstance()->lastWorkingURL = \XLite\Core\URLManager::getCurrentURL();

            $this->redirect($this->buildURL('login'));

        } elseif ($this->isForceChangePassword()
            && !in_array($this->getTarget(), ['force_change_password', 'login'], true)
        ) {
            $this->redirect($this->buildURL('force_change_password'));
        } else {
            if (isset(\XLite\Core\Request::getInstance()->no_https)) {
                \XLite\Core\Session::getInstance()->no_https = true;
            }

            $this->disableUnallowedModules();

            parent::handleRequest();
        }
    }

    /**
     * isBlockContentAllowed
     *
     * @return boolean
     */
    public function isBlockContentAllowed()
    {
        return !$this->isPublicZone();
    }

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

        $auth = \XLite\Core\Auth::getInstance();
        if ($auth->isLogged()
            && $auth->getProfile()->isAdmin() == \XLite::isAdminZone()
        ) {
            //if (count(\XLite\Upgrade\Cell::getInstance()->getEntries())) {
            //    $classes[] = 'upgrade-box-visible';
            //}

            if (!isset($_COOKIE['XCAdminLeftMenuCompressed'])
                || (
                    isset($_COOKIE['XCAdminLeftMenuCompressed'])
                    && $_COOKIE['XCAdminLeftMenuCompressed']
                )
            ) {
                $classes[] = 'left-menu-compressed';
            }
        }

        if ($auth->isAdmin()) {
            $classes[] = 'login-page';
        }

        if ($this->isForceChangePassword()) {
            $classes[] = 'force-change-password-section';
        }

        return $classes;
    }


    /**
     * Get recently logged in admins
     *
     * @return array
     */
    public function getRecentAdmins()
    {
        if ($this->isLogged()
            && null === $this->recentAdmins
        ) {
            $this->recentAdmins = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findRecentAdmins();
        }

        return $this->recentAdmins;
    }

    /**
     * Check if upgrade or update is available on Marketplace.
     *
     * @return boolean
     */
    public function isUpgradeEntryAvailable()
    {
        return false;
        //\XLite\Upgrade\Cell::getInstance()->clear();
        //
        //return (bool) array_filter(
        //    \Includes\Utils\ArrayManager::getObjectsArrayFieldValues(
        //        \XLite\Upgrade\Cell::getInstance()->getEntries(),
        //        'isEnabled'
        //    )
        //);
    }

    /**
     * Returns pricing URL
     *
     * @return string
     */
    public function getPricingURL()
    {
        return \XLite::getXCartURL('https://www.x-cart.com/software_pricing.html');
    }

    /**
     * Call controller action
     *
     * @return void
     */
    protected function callAction()
    {
        parent::callAction();

        if ($this->isLogged()) {
            $this->getContainer()->get('widget_cache_manager')->invalidateBasedOnDatabaseChanges();
        }
    }

    /**
     * Check - is current place public or not
     *
     * @return boolean
     */
    protected function isPublicZone()
    {
        return false;
    }

    /**
     * Mark controller run thread as access denied
     *
     * @return void
     */
    protected function markAsAccessDenied()
    {
        if (\XLite\Core\Auth::getInstance()->isLogged()) {
            parent::markAsAccessDenied();

        } else {
            $this->redirect($this->buildURL('login'));
        }
    }

    /**
     * Disable unallowed modules routine
     *
     * @return void
     */
    protected function disableUnallowedModules()
    {
        if ($this->isAJAX()) {
            return;
        }

        if ($this->getTarget() === 'keys_notice'
            && $this->getAction() === 'recheck'
        ) {
            \XLite\Core\Session::getInstance()->shouldDisableUnallowedModules = false;

        } elseif (!\XLite\Core\Session::getInstance()->shouldDisableUnallowedModules) {
            \XLite\Core\Session::getInstance()->shouldDisableUnallowedModules = true;

        } elseif (\XLite\Core\Session::getInstance()->fraudWarningDisplayed) {
            \XLite\Core\Session::getInstance()->fraudWarningDisplayed = null;
            \XLite\Core\Session::getInstance()->shouldDisableUnallowedModules = null;

            if (!$this->isIgnoreUnallowedModules()) {
                $this->redirect($this->getShopURL('service.php?/disableUnallowedModules'));
            }
        }
    }

    /**
     * Return true if unallowed modules should be ignored on current page
     *
     * @return boolean
     */
    protected function isIgnoreUnallowedModules()
    {
        return false;
    }

    /**
     * Start simplified page to display progress of some process
     *
     * @return void
     */
    protected function startDump()
    {
        parent::startDump();

        if (!isset(\XLite\Core\Request::getInstance()->mode) || 'cp' !== \XLite\Core\Request::getInstance()->mode) {
            $this->displayPageHeader();
        }
    }

    /**
     * Display header of simplified page
     *
     * @param string  $title      Title OPTIONAL
     * @param boolean $scrollDown Scroll down flag OPTIONAL
     *
     * @return void
     */
    protected function displayPageHeader($title = '', $scrollDown = false)
    {
        $output = <<<OUT
<html>
<head>
    <title>$title</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body>

OUT;

        if ($scrollDown) {
            $this->dumpStarted = true;
            $output .= func_refresh_start(false);
        }

        $output .= <<<OUT

<div style='font-size: 12px;'>

OUT;

        echo ($output);
    }

    /**
     * displayPageFooter
     *
     * @return void
     */
    protected function displayPageFooter()
    {
        $urls = (array) $this->getPageReturnURL();

        foreach ($urls as $url) {
            echo ('<br />' . $url . '<br />');
        }

        $output = <<<OUT

</div>

</body>
</html>

OUT;

        echo ($output);
    }

    /**
     * getPageReturnURL
     *
     * @return array
     */
    protected function getPageReturnURL()
    {
        return array();
    }

    /**
     * Sanitize Clean URL
     *
     * @param string $cleanURL Clean URL
     *
     * @return string
     */
    protected function sanitizeCleanURL($cleanURL)
    {
        return substr(trim(preg_replace('/[^a-z0-9 \/\._-]+/Si', '', $cleanURL)), 0, 200);
    }

    /**
     * Check - need use secure protocol or not
     *
     * @return boolean
     */
    public function needSecure()
    {
        return parent::needSecure()
            || (!$this->isHTTPS())
            && \XLite\Core\Config::getInstance()->Security->admin_security;
    }


    /**
     * Do not change it.
     * This method defines the address of the Knowledge base help article
     *
     * @return string
     */
    public function getArticleURL()
    {
        return static::t('https://kb.x-cart.com/general_setup/what_to_do_if_your_store_says_we_are_deploying_new_changes_and_you_cannot_access_it.html');
    }

    // {{{ Search filter methods

    /**
     * Get current search filter
     *
     * @return \XLite\Model\SearchFilter
     */
    public function getSearchFilter()
    {
        $result = null;

        $key = $this->getSearchFilterKeyCell();

        if ($key) {

            if (!empty(\XLite\Core\Request::getInstance()->filter_id)) {
                $id = intval(\XLite\Core\Request::getInstance()->filter_id);
            }

            if (!empty($id)) {
                $result = $this->getSearchFilterByParams(
                    array(
                        'id'        => $id,
                        'filterKey' => $key,
                    )
                );
            }
        }

        return $result;
    }

    /**
     * Get search filter object by specific parameters
     *
     * @param array $params Filter search parameters
     *
     * @return \XLite\Model\SearchFilter
     */
    protected function getSearchFilterByParams($params)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\SearchFilter')->findOneBy($params);
    }

    /**
     * Get search filters key
     *
     * @return string
     */
    public function getSearchFilterKeyCell()
    {
        return 'search-filter-' . $this->getTarget();
    }

    /**
     * Do common action 'save_search_filter'
     *
     * @return void
     */
    protected function doActionSaveSearchFilter()
    {
        if (method_exists($this, 'getSessionCellName') && \XLite\Core\Request::getInstance()->filterName) {

            $cellName = $this->getSessionCellName();
            $searchParams = \XLite\Core\Session::getInstance()->$cellName;

            if (!empty($searchParams)) {

                $key = $this->getSearchFilterKeyCell();

                if (\XLite\Core\Request::getInstance()->search_filter_id) {
                    $params = array();
                    $params['id'] = \XLite\Core\Request::getInstance()->search_filter_id;
                    $params['filterKey'] = $key;

                    // Search for filter
                    $filter = $this->getSearchFilterByParams($params);
                }

                if (empty($filter)) {
                    // Filter not found - create it
                    $filter = new \XLite\Model\SearchFilter();
                    $filter->setFilterKey($key);
                    $filter = \XLite\Core\Database::getRepo('XLite\Model\SearchFilter')->insert($filter);
                }

                // Set the filter name
                $filter->setName(\XLite\Core\Request::getInstance()->filterName);

                // Set search parameters
                $searchParams[static::PARAM_SEARCH_FILTER_ID] = $filter->getId();
                $filter->setParameters($searchParams);

                \XLite\Core\Session::getInstance()->$cellName = $searchParams;

                \XLite\Core\TopMessage::addInfo('Filter has been successfully saved');

                \XLite\Core\Database::getEM()->flush();
            }
        }

        $this->setReturnURL($this->buildURL($this->getTarget()));
    }

    /**
     * Do common action 'save_search_filter'
     *
     * @return void
     */
    protected function doActionDeleteSearchFilter()
    {
        if (method_exists($this, 'getSessionCellName') && 0 < intval(\XLite\Core\Request::getInstance()->filter_id)) {
            $params = array();
            $params['id'] = intval(\XLite\Core\Request::getInstance()->filter_id);
            $params['filterKey'] = $this->getSearchFilterKeyCell();

            // Search for filter
            $filter = $this->getSearchFilterByParams($params);

            if ($filter) {
                $filter->delete();
                \XLite\Core\TopMessage::addInfo('Filter has been removed');
            }
        }

        $this->setReturnURL($this->buildURL($this->getTarget()));
    }

    /**
     * Get parameters of the requested filter
     *
     * @return array
     */
    protected function getSearchFilterParams()
    {
        $result = array();

        $filter = $this->getSearchFilter();

        // Reset search filter
        if ($filter) {
            $result = $filter->getParameters();

            if ($filter->getId()
                && (
                    empty($result[static::PARAM_SEARCH_FILTER_ID])
                    || $filter->getId() != $result[static::PARAM_SEARCH_FILTER_ID]
                )
            ) {
                // Correct saved in the parameters value of searchFilterId
                $result[static::PARAM_SEARCH_FILTER_ID] = $filter->getId();
                $filter->setParameters($result);
            }
        }

        return $result;
    }

    /**
     * Get current search filter from items list's parameters saved in session cell
     *
     * @return \XLite\Model\SearchFilter
     */
    public function getCurrentSearchFilter()
    {
        $result = null;

        if (method_exists($this, 'getSessionCellName')) {
            $cellName = $this->getSessionCellName();
            $searchParams = \XLite\Core\Session::getInstance()->$cellName;
            if (isset($searchParams[static::PARAM_SEARCH_FILTER_ID])) {
                $result = $this->getSearchFilterByParams(
                    array(
                        'id' => $searchParams[static::PARAM_SEARCH_FILTER_ID],
                        'filterKey' => $this->getSearchFilterKeyCell(),
                    )
                );
            }
        }

        return $result;

    }

    /**
     * Return true if currently used filter is the same as secified
     *
     * @param mixed $fid Filter ID (may be integer or string)
     *
     * @return boolean
     */
    public function isSelectedFilter($fid)
    {
        $result = false;

        if (method_exists($this, 'getSessionCellName')) {
            $cellName = $this->getSessionCellName();
            $searchParams = \XLite\Core\Session::getInstance()->$cellName;
            $result = isset($searchParams[static::PARAM_SEARCH_FILTER_ID])
                && $searchParams[static::PARAM_SEARCH_FILTER_ID] == $fid;
        }

        return $result;
    }

    // }}}

    // {{{ Location, Breadcrumbs

    /**
     * Return current location path
     *
     * @return \XLite\View\Location
     */
    public function getLocationPath()
    {
        if (null === $this->locationPath) {
            $this->defineLocationPath();
        }

        return $this->locationPath;
    }

    /**
     * Method to create the location line
     *
     * @return void
     */
    protected function defineLocationPath()
    {
        $this->locationPath = array();

        // Ability to add part to the line
        $this->addBaseLocation();

        // Ability to define last element in path via short function
        $location = $this->getLocation();

        if ($location) {
            $this->addLocationNode($location);
        }
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
    }

    /**
     * Add node to the location line
     *
     * @param string $name     Node title
     * @param string $link     Node link OPTIONAL
     * @param array  $subnodes Node subnodes OPTIONAL
     *
     * @return void
     */
    protected function addLocationNode($name, $link = null, array $subnodes = null)
    {
        $this->locationPath[] = \XLite\View\Location\Node::create($name, $link, $subnodes);
    }

    // }}}
}
