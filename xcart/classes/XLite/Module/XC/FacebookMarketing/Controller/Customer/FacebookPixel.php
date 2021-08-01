<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Controller\Customer;

use Xlite\Core\Session;

/**
 * FacebookPixel
 */
class FacebookPixel extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Mark cart as initiated
     */
    protected function doActionInitiateCheckout()
    {
        $this->set('silent', true);
        $this->setSuppressOutput(true);
        Session::getInstance()->setPixelLastInitiatedCart(\XLite\Model\Cart::getInstance()->getOrderId());
    }

    /**
     * Print current cart data
     */
    protected function doActionRetrieveCurrentCartData()
    {
        $this->set('silent', true);
        $this->setSuppressOutput(true);
        $result = [];

        if ($cart = $this->getCart()) {
            $result['order_total'] = $cart->getTotal();

            if ($currency = $cart->getCurrency() ?: \XLite::getInstance()->getCurrency()) {
                $result['order_currency_code'] = $currency->getCode();
            } else {
                $result['order_currency_code'] = \XLite\View\Model\Currency\Currency::DEFAULT_CURRENCY;
            }
        }

        $this->displayJSON($result);
    }
}