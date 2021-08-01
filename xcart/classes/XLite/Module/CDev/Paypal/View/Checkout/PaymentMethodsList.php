<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Checkout;

/**
 * Shipping methods list
 */
class PaymentMethodsList extends \XLite\View\Checkout\PaymentMethodsList implements \XLite\Base\IDecorator
{
    /**
     * Return list of available payment methods
     *
     * @return array
     */
    protected function getPaymentMethods()
    {
        if (\XLite\Core\Request::getInstance()->ec_returned) {

            return $this->getCart()->getOnlyExpressCheckoutIfAvailable();

        } elseif ($this->isReturnedAfterPaypalCommercePlatform()) {

            return $this->getCart()->getOnlyCommercePlatformIfAvailable();
        }

        return $this->getCart()->getPaymentMethods();
    }
}
