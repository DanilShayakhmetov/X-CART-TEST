<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Button\Checkout;


/**
 * PaypalCredit
 *
 * @ListChild (list="checkout.review.selected.placeOrder", weight="450")
 * @ListChild (list="checkout_fastlane.sections.place-order.before", weight="100")
 */
class PaypalCredit extends \XLite\Module\CDev\Paypal\View\Button\AExpressCheckout
{
    protected function isVisible()
    {
        /** @var \XLite\Model\Cart $cart */
        $cart = $this->getCart();

        return parent::isVisible()
            && \XLite\Module\CDev\Paypal\Main::isPaypalCreditEnabled($cart);
    }

    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/CDev/Paypal/button/js/credit.js',
            'modules/CDev/Paypal/button/js/checkout_credit.js'
        ]);
    }

    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' pp-style-credit paypal-ec-checkout-credit';
    }

    /**
     * @return string
     */
    protected function getButtonStyleNamespace()
    {
        return 'credit';
    }

    /**
     * @return string
     */
    protected function getButtonLayout()
    {
        return 'horizontal';
    }

    /**
     * @return string
     */
    protected function getButtonColor()
    {
        $configVariable = $this->getButtonStyleNamespace() . '_style_color';

        return \XLite\Core\Config::getInstance()->CDev->Paypal->{$configVariable} ?: 'darkblue';
    }
}
