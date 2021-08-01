<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Button\Checkout;

/**
 * Express Checkout button
 *
 * @ListChild (list="checkout.review.selected.placeOrder", weight="450")
 * @ListChild (list="checkout_fastlane.sections.place-order.before", weight="100")
 */
class ExpressCheckout extends \XLite\Module\CDev\Paypal\View\Button\AExpressCheckout
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/CDev/Paypal/button/js/checkout.js',
        ]);
    }

    /**
     * @return string
     */
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' paypal-ec-checkout';
    }

    /**
     * Returns true if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return \XLite\Module\CDev\Paypal\Main::isExpressCheckoutEnabled($this->getCart());
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
    protected function getButtonAdditionalParams()
    {
        $result = parent::getButtonAdditionalParams();

        if (\XLite\Core\Config::getInstance()->CDev->Paypal->funding_card !== null) {
            $result['data-funding-card'] = (bool) \XLite\Core\Config::getInstance()->CDev->Paypal->funding_card ?: false;
        } else {
            $result['data-funding-card'] = true;
        }

        if (\XLite\Core\Config::getInstance()->CDev->Paypal->funding_elv !== null) {
            $result['data-funding-elv'] = (bool) \XLite\Core\Config::getInstance()->CDev->Paypal->funding_elv ?: false;
        } else {
            $result['data-funding-elv'] = true;
        }

        if (\XLite\Core\Config::getInstance()->CDev->Paypal->funding_venmo !== null) {
            $result['data-funding-venmo'] = (bool) \XLite\Core\Config::getInstance()->CDev->Paypal->funding_venmo ?: false;
        } else {
            $result['data-funding-venmo'] = true;
        }

        return $result;
    }
}
