<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Controller\Customer;

/**
 * Ordered files
 */
class OrderedFiles extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    protected function checkAccess()
    {
        return $this->orderWithEgoodsExists() && parent::checkAccess();
    }

    /**
     * Define current location for breadcrumbs
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::t('Ordered files');
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(static::t('My account'));
    }

    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->checkAccess()
            ? static::t('Ordered files')
            : null;
    }

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return \XLite\Core\Request::getInstance()->widget;
    }

    /**
     * Get orders with files
     *
     * @return array
     */
    public function getOrdersWithFiles()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Order')->findAllOrdersWithEgoods($this->getProfile(), false);
    }

    /**
     * Checks if there is an order for the profile
     *
     * @return boolean
     */
    public function orderWithEgoodsExists()
    {
        $cnd = new \XLite\Core\CommonCell;
        $cnd->user = $this->getProfile();
        $cnd->limit = [0, 1];

        if (\XLite\Core\Database::getRepo('XLite\Model\Order')->searchOrdersWithEgoods($cnd, true) > 0) {
            return true;
        }

        return false;
    }
}
