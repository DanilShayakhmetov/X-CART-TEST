<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Button;

use \XLite\Module\XPay\XPaymentsCloud\Core\ApplePay as XPaymentsApplePay;

/**
 * Checkout buttons separator
 */
class AButtonsSeparator extends \XLite\View\Button\ButtonsSeparator
{
    /**
     * Checks if Checkout with Apple Pay is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $cart = $this->getNotEmptyCart();

        return parent::isVisible()
            && XPaymentsApplePay::isCheckoutWithApplePayEnabled($cart);
    }

    /**
     * Checks current cart and return it only if it is not empty
     *
     * @return \XLite\Model\Cart
     */
    protected function getNotEmptyCart()
    {
        return XPaymentsApplePay::getNotEmptyCart($this->getCart());
    }
}
