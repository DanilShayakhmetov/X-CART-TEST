<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Controller\Customer;

/**
 * PinCodes
 *
 */
class PinCodes extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    protected function checkAccess()
    {
        if ($this->isLogged()) {
            $cnd = new \XLite\Core\CommonCell;
            $cnd->user = \XLite\Core\Auth::getInstance()->getProfile();

            $count = \XLite\Core\Database::getRepo('XLite\Model\Order')->searchWithPinCodes($cnd, true);

            if (!$count) {
                return false;
            }
        }

        return parent::checkAccess();
    }

    /**
     * Define current location for breadcrumbs
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::t('PIN codes');
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode('My account');
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
            ? static::t('PIN codes')
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
}
