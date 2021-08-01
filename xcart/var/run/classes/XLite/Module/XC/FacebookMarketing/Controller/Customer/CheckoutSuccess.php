<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Controller\Customer;


/**
 * Checkout success page
 */
 class CheckoutSuccess extends \XLite\Controller\Customer\CheckoutSuccessAbstract implements \XLite\Base\IDecorator
{
    /**
     * Print current cart data
     */
    protected function doActionPixelRetrieveOrderData()
    {
        $this->set('silent', true);
        $this->setSuppressOutput(true);

        $result = [];

        if ($order = $this->getOrder()) {
            $result['order_total'] = $order->getTotal();

            if ($currency = $order->getCurrency() ?: \XLite::getInstance()->getCurrency()) {
                $result['order_currency_code'] = $currency->getCode();
            } else {
                $result['order_currency_code'] = \XLite\View\Model\Currency\Currency::DEFAULT_CURRENCY;
            }
        }

        $this->displayJSON($result);
    }
}