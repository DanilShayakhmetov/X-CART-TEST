<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Button\Minicart;

/**
 * Express Checkout button
 *
 * @ListChild (list="minicart.horizontal.buttons", weight="100")
 */
class PaypalCommercePlatform extends \XLite\Module\CDev\Paypal\View\Button\APaypalCommercePlatform
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/CDev/Paypal/button/paypal_commerce_platform/mini_cart.js',
        ]);
    }

    /**
     * Returns true if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        /** @var \XLite\Model\Cart $cart */
        $cart = $this->getCart();

        return parent::isVisible()
            && $cart
            && (0 < $cart->getTotal())
            && $cart->checkCart();
    }

    /**
     * @return string
     */
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' cart-checkout pcp-mini-cart';
    }

    /**
     * @return string
     */
    protected function getButtonStyleNamespace()
    {
        return 'mini_cart';
    }
}
