<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ContactUs\Controller\Customer;

use XLite\Core\Config;

/**
 * Contact us controller
 */
class ContactUs extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && Config::getInstance()->CDev->ContactUs->enable_form;
    }

    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return Config::getInstance()->Security->customer_security;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Contact us');
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
     * Return class name of the register form
     *
     * @return string|void
     */
    protected function getModelFormClass()
    {
        return '\XLite\Module\CDev\ContactUs\View\Model\Contact';
    }

    /**
     * Send message
     *
     * @return void
     */
    protected function doActionSend()
    {
        $this->getModelForm()->performAction('update');
    }
}
