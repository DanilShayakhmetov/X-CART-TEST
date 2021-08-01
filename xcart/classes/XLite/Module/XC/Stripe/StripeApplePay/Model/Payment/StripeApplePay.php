<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\StripeApplePay\Model\Payment;

use XLite\Model\Order;
use XLite\Model\Payment\Method;

/**
 * StripeApplePay payment processor
 */
class StripeApplePay extends \XLite\Module\XC\Stripe\Model\Payment\Stripe
{
    protected static $allowedCustomerCountries = ['AE','AT','AU','BE','BR','CA','CH','DE','DK','EE','ES','FI','FR','GB','GR','HK','IE','IN','IT','JP','LT','LU','LV','MX','MY','NL','NO','NZ','PH','PL','PT','RO','SE','SG','SI','SK','US'];

    /**
     * Check - payment method is configured or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        $parent_method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findOneBy(['service_name' => 'Stripe']);

        if ($this->isTestMode($method)) {
            $parent_is_configured = ($parent_method->getSetting('accessTokenTest') && $parent_method->getSetting('publishKeyTest'));
        } else {
            $parent_is_configured = ($parent_method->getSetting('accessToken') && $parent_method->getSetting('publishKey'));
        }

        return $parent_is_configured && \XLite\Core\Config::getInstance()->Security->customer_security;
    }


    /**
     * Check For Customer Countries
     *
     * @param Order $order Order
     * @param Method $method Payment method
     *
     * @return boolean
     */
    public function isApplicable(Order $order, Method $method)
    {//{{{
        return
            in_array($this->getCustomerCountryCode($order), static::$allowedCustomerCountries)
            && parent::isApplicable($order, $method);
    }//}}}

    /**
     * @return string
     */
    public function getCustomerCountryCode(Order $_order)
    {//{{{
        if ($_order && $_order->getProfile()) {
            return ($profile_addr = $_order->getProfile()->getBillingAddress())
                ? $profile_addr->getCountry()->getCode()
                : (\XLite\Model\Address::getDefaultFieldValue('country') ? \XLite\Model\Address::getDefaultFieldValue('country')->getCode() : 'US');
        }

        return '';
    }//}}}

    /**
     * Get settings widget or template
     *
     * @return string Widget class name or template path
     */
    public function getSettingsWidget()
    {
        return '\XLite\Module\XC\Stripe\StripeApplePay\View\Config';
    }

    /**
     * Get input template
     *
     * @return string
     */
    public function getInputTemplate()
    {
        // We need to show our button below main 'place order' button.Listchild is used
        return 'modules/XC/Stripe/StripeApplePay/checkout/reloadable_model_template.twig';
    }

    /**
     * Return true if payment method settings form should use default submit button.
     * Otherwise, settings widget must define its own button
     *
     * @return boolean
     */
    public function useDefaultSettingsFormButton()
    {
        return true;
    }
}
