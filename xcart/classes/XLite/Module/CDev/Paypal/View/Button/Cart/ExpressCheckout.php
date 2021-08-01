<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Button\Cart;

/**
 * Express Checkout button
 *
 * @ListChild (list="cart.panel.totals", weight="100")
 */
class ExpressCheckout extends \XLite\Module\CDev\Paypal\View\Button\AExpressCheckout
{
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
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/button/cart/ec_button.twig';
    }

    /**
     * @return string
     */
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' paypal-ec'
            . (
            \XLite\Module\CDev\Paypal\Main::isPaypalCreditEnabled($this->getCart())
                ? ' pp-funding-credit'
                : ''
            );
    }

    /**
     * @return string
     */
    protected function getButtonStyleNamespace()
    {
        return 'cart';
    }
}
