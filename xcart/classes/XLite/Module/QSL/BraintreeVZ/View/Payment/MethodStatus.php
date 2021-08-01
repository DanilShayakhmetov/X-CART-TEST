<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\View\Payment;

/**
 * Payment method status
 */
class MethodStatus extends \XLite\View\Payment\MethodStatus implements \XLite\Base\IDecorator
{
    /**
     * Hack against X-Cart. 
     * Status bar should be visible only once, when the bar is displayed second time
     */
    protected static $_visible = false;

    /**
     * Check if this is Braintree payment method
     *
     * @return bool 
     */
    protected function isBraintreePaymentMethod()
    {
        return $this->getPaymentMethod() 
            && $this->getPaymentMethod()->getClass() == \XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::BRAINTREE_CLASS;
    }

    /**
     * Checks whether the widget is visible, or not
     *
     * @return boolean
     */
    protected function isVisible()
    {
        if ($this->isBraintreePaymentMethod()) {

            $result = true;
            if (!static::$_visible) {
                static::$_visible = true;
                $result = false;
            }

        } else {
            $result = parent::isVisible();
        }

        return $result;
    }
}
