<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Form\Product;

/**
 * Extra params for Add To Cart form used by Buy With Apple Pay button
 */
 class AddToCart extends \XLite\View\Form\Product\AddToCartAbstract implements \XLite\Base\IDecorator
{
    /**
     * getFormDefaultParams
     *
     * @return array
     */
    protected function getFormDefaultParams()
    {
        $list = parent::getFormDefaultParams();

        if (\XLite\Module\XPay\XPaymentsCloud\Core\ApplePay::isCheckoutWithApplePayEnabled()) {
            $list['xpaymentsBuyWithApplePay'] = false;
        }

        return $list;
    }
}
