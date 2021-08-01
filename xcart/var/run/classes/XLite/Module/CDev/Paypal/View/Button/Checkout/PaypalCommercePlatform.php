<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Button\Checkout;

use XLite\Module\CDev\Paypal;

/**
 * Express Checkout button
 *
 * @ListChild (list="checkout.review.selected.placeOrder", weight="370")
 * @ListChild (list="checkout_fastlane.sections.place-order.before", weight="100")
 */
class PaypalCommercePlatform extends \XLite\Module\CDev\Paypal\View\Button\APaypalCommercePlatform
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        $result = parent::getJSFiles();
        $result[] = 'modules/CDev/Paypal/button/paypal_commerce_platform/checkout.js';
        $result[] = 'modules/CDev/Paypal/button/paypal_commerce_platform/hosted_fields.js';

        return $result;
    }

    protected function isVisible()
    {
        return parent::isVisible() && !\XLite::getController()->isReturnedAfterPaypalCommercePlatform();
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/button/paypal_commerce_platform/checkout.twig';
    }

    /**
     * @return string
     */
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' pcp-checkout';
    }

    /**
     * @return string
     */
    protected function getButtonStyleNamespace()
    {
        return 'checkout';
    }

    /**
     * @return array
     */
    protected function getHostedFieldsParams()
    {
        $method = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PCP);

        return [
            '3d_secure_soft_exception' => (bool) $method->getSetting('3d_secure_soft_exception')
        ];
    }

    /**
     * @return string
     */
    protected function getDefaultLabel()
    {
        $cart = $this->getCart();

        $value = $cart->getFirstOpenPaymentTransaction()
            ? $cart->getFirstOpenPaymentTransaction()->getValue()
            : $cart->getTotal();

        return static::t(
            'Place order X',
            array(
                'total' => $this->formatPrice(
                    $value,
                    $cart->getCurrency(),
                    true
                ),
            )
        );
    }
}
