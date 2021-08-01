<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\Module\CDev\Paypal\Model\Payment\Processor;

use XLite\Module\XC\NotFinishedOrders\Main;

/**
 * @Decorator\Depend ("CDev\Paypal")
 */
 class ExpressCheckoutMerchantAPI extends \XLite\Module\CDev\Paypal\Model\Payment\Processor\ExpressCheckoutMerchantAPIAbstract implements \XLite\Base\IDecorator
{
    /**
     * Perform 'SetExpressCheckout' request and get Token value from Paypal
     *
     * @param \XLite\Model\Payment\Method           $method Payment method
     * @param \XLite\Model\Payment\Transaction|null $transaction
     *
     * @return string
     */
    public function doSetExpressCheckout(\XLite\Model\Payment\Method $method, \XLite\Model\Payment\Transaction $transaction = null)
    {
        $result = parent::doSetExpressCheckout($method, $transaction);

        if (Main::isCreateOnPlaceOrder()) {
            $cart = \XLite\Model\Cart::getInstance();
            $cart->processNotFinishedOrder(true);
        }

        return $result;
    }
}
