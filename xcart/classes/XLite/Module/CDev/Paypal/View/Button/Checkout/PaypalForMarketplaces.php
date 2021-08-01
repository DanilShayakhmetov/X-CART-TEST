<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Button\Checkout;

use XLite\Core\PreloadedLabels\ProviderInterface;
use XLite\Module\CDev\Paypal;

/**
 * Express Checkout button
 *
 * @ListChild (list="checkout.review.selected.placeOrder", weight="450")
 * @ListChild (list="checkout_fastlane.sections.place-order.before", weight="100")
 */
class PaypalForMarketplaces extends \XLite\Module\CDev\Paypal\View\Button\Checkout\ExpressCheckout implements ProviderInterface
{
    /**
     * @return string
     */
    protected function getButtonClass()
    {
        $result = str_replace('paypal-ec-checkout', 'paypal-checkout-for-marketplaces', parent::getButtonClass());

        if (!$this->isCheckoutAvailable()) {
            $result .= ' unavailable';
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getButtonAdditionalParams()
    {
        $result                = parent::getButtonAdditionalParams();
        $result['data-method'] = Paypal\Main::PP_METHOD_PFM;

        return $result;
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/button/paypal_for_marketplaces/ec_button.twig';
    }

    /**
     * @return bool
     */
    protected function isCheckoutAvailable()
    {
        $method = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PFM);

        return $method->getProcessor()->isCheckoutAvailable($method, $this->getCart());
    }

    /**
     * Returns true if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return \XLite\Module\CDev\Paypal\Main::isPaypalForMarketplacesEnabled($this->getCart());
    }

    /**
     * Array of labels in following format.
     *
     * 'label' => 'translation'
     *
     * @return mixed
     */
    public function getPreloadedLanguageLabels()
    {
        $method = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PFM);

        return [
            'We are experiencing a problem with the "PayPal For Marketplaces" payment method.' =>
                (string) static::t(
                    'We are experiencing a problem with the "PayPal For Marketplaces" payment method.',
                    ['payment_method_name' => $method->getTitle()]
                ),
        ];
    }
}
