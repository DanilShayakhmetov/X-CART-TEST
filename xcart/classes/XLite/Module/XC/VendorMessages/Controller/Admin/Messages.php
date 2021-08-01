<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Controller\Admin;

/**
 * Messages
 */
class Messages extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = ['target'];

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return static::t('Messages');
    }

    /**
     * @inheritdoc
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders')
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage conversations');
    }

    /**
     * Check - search box is visible or not
     *
     * @return boolean
     */
    public function isSearchVisible()
    {
        return 0 < \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')->count();
    }

    /**
     * @return boolean
     */
    public function hasResults()
    {
        $itemsList = new \XLite\Module\XC\VendorMessages\View\ItemsList\Admin\Conversations();

        return $itemsList->hasResultsPublic();
    }

    /**
     * Get search condition parameter by name
     *
     * @param string $paramName Parameter name
     *
     * @return mixed
     */
    public function getCondition($paramName)
    {
        $searchParams = $this->getConditions();

        return isset($searchParams[$paramName])
            ? $searchParams[$paramName]
            : null;
    }

    /**
     * @inheritdoc
     */
    public function isRedirectNeeded()
    {
        return parent::isRedirectNeeded() || ($this->getAction() == 'search' && !$this->silent);
    }

    /**
     * Save search conditions
     */
    protected function doActionSearch()
    {
        $cellName = \XLite\Module\XC\VendorMessages\View\ItemsList\Admin\Conversations::getSessionCellName();

        \XLite\Core\Session::getInstance()->$cellName = $this->getSearchParams();
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        $searchParams = $this->getConditions();

        foreach (
            \XLite\Module\XC\VendorMessages\View\ItemsList\Admin\Conversations::getSearchParams() as $requestParam
        ) {
            if (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                $searchParams[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
            }
        }

        return $searchParams;
    }

    /**
     * Get search conditions
     *
     * @return array
     */
    protected function getConditions()
    {
        $cellName = \XLite\Module\XC\VendorMessages\View\ItemsList\Admin\Conversations::getSessionCellName();

        $searchParams = \XLite\Core\Session::getInstance()->$cellName;

        if (!is_array($searchParams)) {
            $searchParams = [];
        }

        return $searchParams;
    }

    /**
     * Mark conversations as read
     */
    public function doActionMarkConversationsRead()
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();

        if (
            !empty(\XLite\Core\Request::getInstance()->select)
            && is_array(\XLite\Core\Request::getInstance()->select)
        ) {
            $ids = array_keys(\XLite\Core\Request::getInstance()->select);

            \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Conversation')->markRead($ids, $profile);
        } else {
            \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Conversation')->markReadAll($profile);
        }
    }

    /**
     * Unmark conversations as read
     */
    public function doActionMarkConversationsUnread()
    {
        $profile = \XLite\Core\Auth::getInstance()->getProfile();

        if (
            !empty(\XLite\Core\Request::getInstance()->select)
            && is_array(\XLite\Core\Request::getInstance()->select)
        ) {
            $ids = array_keys(\XLite\Core\Request::getInstance()->select);

            \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Conversation')->markUnread($ids, $profile);
        } else {
            \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Conversation')->markUnreadAll($profile);
        }
    }
}
