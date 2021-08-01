<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Controller\Customer;

use XLite\Module\XPay\XPaymentsCloud\Model\Payment\Processor\XPaymentsCloud;

/**
 * Checkout controller
 */
 class CheckoutPayment extends \XLite\Controller\Customer\CheckoutPaymentAbstract implements \XLite\Base\IDecorator
{
    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        if (
            $this->getCart()->getPaymentMethod()
            && $this->getCart()->getPaymentMethod()->getProcessor() instanceof XPaymentsCloud
            || $this->isXpaymentsCardSetup()
        ) {
            $result = '';
        } else {
            $result = parent::getTitle();
        }

        return $result;
    }

    /**
     * We need to override this check to make sure Card Setup will work
     * (even if cart is empty)
     *
     * @return boolean
     */
    public function isCheckoutAvailable()
    {
        if ($this->isXpaymentsCardSetup()) {
            $controllerCheckout = new \XLite\Controller\Customer\Checkout();
            $result = $controllerCheckout->isCheckoutAvailable();
        } else {
            $result = parent::isCheckoutAvailable();
        }

        return $result;
    }

    /**
     * Check if page is used for Card Setup
     *
     * @return bool
     */
    protected function isXpaymentsCardSetup()
    {
        return ('CardSetup' == \XLite\Core\Request::getInstance()->mode);
    }
}
