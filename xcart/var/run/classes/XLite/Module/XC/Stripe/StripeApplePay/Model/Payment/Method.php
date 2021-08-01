<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\StripeApplePay\Model\Payment;

/**
 * Payment method model. Used to inherit settings from parent stripe method
 */
 class Method extends \XLite\Module\XPay\XPaymentsCloud\Model\Payment\Method implements \XLite\Base\IDecorator
{
    /**
     * Get payment processor class
     *
     * @return string
     */
    public function getClass()
    {
        $class = parent::getClass();

        if ('StripeApplePay' == $this->getServiceName()) {
            $class = 'Module\XC\Stripe\StripeApplePay\Model\Payment\StripeApplePay';
        }

        return $class;
    }

    /**
     * Get payment method setting by its name
     *
     * @param string $name Setting name
     *
     * @return string
     */
    public function getSetting($name)
    {
        $result = parent::getSetting($name);
        if (!$result && 'StripeApplePay' === $this->getServiceName()) {
            $parentMethod = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findOneBy(['service_name' => 'Stripe']);

            $result = $parentMethod->getSetting($name);
        }

        return $result;
    }

    /**
     * Show link to parent method in admin
     *
     * @return string
     */
    public function getNotSwitchableReason()
    {
        if (
            'StripeApplePay' == $this->getServiceName()
            && $parent_mid = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findOneBy(['service_name' => 'Stripe'])
        ) {
            if (!\XLite\Core\Config::getInstance()->Security->customer_security) {
                return static::t(
                    'Payments with this payment method are not allowed because HTTPS is not configured',
                    [ 'url' => \XLite\Core\Converter::buildURL('https_settings')]
                );
            } else {
                $url = \XLite\Core\Converter::buildURL('payment_method', '', ['method_id' => $parent_mid->getMethodId()]);
                return static::t('Firstly, you have to connect to Stripe <a href="{{admin_link}}" target="_blank">here</a>', ['admin_link' => $url]);
            }
        }

        return parent::getNotSwitchableReason();
    }
}
