<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\StripeApplePay\View\Button;

/**
 * Stripe Apple checkout button
 */
class PayWithStripe extends \XLite\View\Button\Link
{
    /**
     * @return boolean
     */
    protected function isVisible()
    {
        $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findOneBy(['service_name' => 'StripeApplePay']);

        return parent::isVisible() && $method && $method->isEnabled();
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Stripe/StripeApplePay/checkout/pay_with_stripe_button.twig';
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/Stripe/StripeApplePay/button.css';
        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = [
            'url' => 'https://js.stripe.com/v3/',
        ];
        $list[] = 'modules/XC/Stripe/StripeApplePay/payment.js';
        return $list;
    }
}
