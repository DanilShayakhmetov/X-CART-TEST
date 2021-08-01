<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Separate checkout payment page
 */
class CheckoutPayment extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Checkout');
    }

    /**
     * Define if the checkout is available
     * On the other hand the sign-in page is available only
     *
     * @return boolean
     */
    public function isCheckoutAvailable()
    {
        $controllerCheckout = new \XLite\Controller\Customer\Checkout();

        return $controllerCheckout->isCheckoutAvailable()
            && $this->getCart()->checkCart();
    }

    /**
     * Go to cart view if cart is empty
     *
     * @return void
     */
    public function handleRequest()
    {
        if (!$this->isCheckoutAvailable()) {
            $this->setHardRedirect();
            $this->setReturnURL($this->buildURL('cart'));
            $this->doRedirect();
        }

        parent::handleRequest();
    }

    /**
     * isSecure
     * TODO: check if this method is used
     *
     * @return boolean
     */
    public function isSecure()
    {
        return $this->is('HTTPS') ? true : parent::isSecure();
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
     * Check - is service controller or not
     *
     * @return boolean
     */
    protected function isServiceController()
    {
        return true;
    }
}
