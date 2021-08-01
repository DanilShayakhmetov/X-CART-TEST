<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Model;

use XLite\Module\XPay\XPaymentsCloud\Main;

/**
 * XPaymentsCloud Subscription Plan form model
 *
 */
class SubscriptionPlan extends \XLite\View\Model\AModel
{
    /**
     * schemeDefault
     *
     * @var array
     */
    protected $schemaDefault = [
        'is_subscription'       => [
            self::SCHEMA_CLASS                                          => 'XLite\View\FormField\Input\Checkbox\OnOff',
            self::SCHEMA_LABEL                                          => 'This is subscription product',
            \XLite\View\FormField\Input\Checkbox\OnOff::PARAM_ON_LABEL  => 'Yes',
            \XLite\View\FormField\Input\Checkbox\OnOff::PARAM_OFF_LABEL => 'No',
        ],
        'setup_fee'          => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text\Price',
            self::SCHEMA_LABEL => 'Setup fee',
            self::SCHEMA_HELP  => 'The product\'s selling price will be calculated as Setup Fee + Subscription Fee. If you do not intend to charge any subscription setup fee to the buyer, set the Setup fee to "0" (zero); in this case, the product\'s selling price will equal the Subscription fee.',
        ],
        'fee'                => [
            self::SCHEMA_CLASS                                       => 'XLite\View\FormField\Input\Text\Price',
            self::SCHEMA_LABEL                                       => 'Subscription fee',
            self::SCHEMA_HELP                                        => 'Amount the buyer will need to pay for each period for the duration of the subscription. If subscription fee is set to "0" (zero), buyers will not be charged when creating recurring orders.',
            self::SCHEMA_REQUIRED                                    => true,
            \XLite\View\FormField\Input\Text\Base\Numeric::PARAM_MIN => \XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Plan::MIN_FEE_VALUE,
        ],
        'plan'               => [
            self::SCHEMA_CLASS => 'XLite\Module\XPay\XPaymentsCloud\View\FormField\Plan',
            self::SCHEMA_LABEL => 'Plan',
        ],
        'periods'            => [
            self::SCHEMA_CLASS => 'XLite\View\FormField\Input\Text\Integer',
            self::SCHEMA_LABEL => 'Re-bill periods',
            self::SCHEMA_HELP  => 'Number of times that the buyer should be re-billed for this product after the initial payment. Set to "0" (zero) if you need an infinite subscription.',
        ],
        'calculate_shipping' => [
            self::SCHEMA_CLASS                                          => 'XLite\View\FormField\Input\Checkbox\OnOff',
            self::SCHEMA_LABEL                                          => 'Calculate shipping for recurring orders',
            \XLite\View\FormField\Input\Checkbox\OnOff::PARAM_ON_LABEL  => 'Yes',
            \XLite\View\FormField\Input\Checkbox\OnOff::PARAM_OFF_LABEL => 'No',
        ],
    ];

    /**
     * Update subscription plan
     *
     * @return void
     */
    public function updateSubscriptionPlan()
    {
        $product = $this->getProduct();
        $subscriptionPlan = $product->getXpaymentsSubscriptionPlan();

        $repo = \XLite\Core\Database::getRepo('\XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Plan');
        $data = $this->getRequestData();

        if (\XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Plan::MIN_FEE_VALUE > floatval($data['fee'])) {
            $data['is_subscription'] = false;
        }

        if (empty($data['plan']['reverse'])) {
            $data['plan']['reverse'] = false;
        }

        if (is_null($subscriptionPlan)) {
            $data['product'] = $product;

            $repo->insert($data);

        } else {
            $repo->update($subscriptionPlan, $data);
        }
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();
        $result['submit'] = new \XLite\View\Button\Submit(
            [
                \XLite\View\Button\AButton::PARAM_LABEL => 'Save',
                \XLite\View\Button\AButton::PARAM_STYLE => Main::isXpaymentsSubscriptionsConfiguredAndActive() ? 'action' : 'action hidden',
            ]
        );

        return $result;
    }

    /**
     * This object will be used if another one is not passed
     *
     * @return \XLite\Model\AEntity
     */
    protected function getDefaultModelObject()
    {
        $product = $this->getProduct();

        $subscriptionPlan = is_null($product)
            ? null
            : $product->getXpaymentsSubscriptionPlan();

        if (!is_null($product) && is_null($subscriptionPlan)) {
            $subscriptionPlan = new \XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Plan();
            $subscriptionPlan->setSetupFee($product->getPrice());
            $subscriptionPlan->setCalculateShipping(true);
        }

        return $subscriptionPlan;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\Module\XPay\XPaymentsCloud\View\Form\SubscriptionPlan';
    }

}
