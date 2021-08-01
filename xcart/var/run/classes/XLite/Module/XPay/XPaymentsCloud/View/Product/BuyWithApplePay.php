<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Product;

/**
 * Buy with Apple Pay widget
 */
class BuyWithApplePay extends \XLite\View\Product\Details\Customer\Widget
{
    /**
     * Is widget visible
     *
     * @return bool
     */
    protected function isVisible()
    {
        return
            parent::isVisible()
            && \XLite\Module\XPay\XPaymentsCloud\Core\ApplePay::isCheckoutWithApplePayEnabled();
    }

    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-product-buy-apple-pay-button';
    }

    /**
     * Return directory contains the template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XPay/XPaymentsCloud/product/details/buy_apple_pay_widget.twig';
    }
}