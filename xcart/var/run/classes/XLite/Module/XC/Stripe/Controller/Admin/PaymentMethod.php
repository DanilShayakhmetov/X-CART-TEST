<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\Controller\Admin;

/**
 * Payment method
 */
abstract class PaymentMethod extends \XLite\Module\XPay\XPaymentsCloud\Controller\Admin\PaymentMethod implements \XLite\Base\IDecorator
{

    /**
     * Check visibility
     *
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getPaymentMethod();
    }

    /**
     * Run controller
     *
     * @return void
     */
    protected function run()
    {
        if (!$this->getAction()) {
            $method = $this->getPaymentMethod();
            if (
                $method->getProcessor() instanceOf \XLite\Module\XC\Stripe\Model\Payment\Stripe
                && $method->getSetting('accessToken')
                && !$method->getProcessor()->retrieveAcount()
            ) {

                $prefix = $method->getProcessor()->isTestMode($method) ? 'Test' : '';
                $method->setSetting('accessToken' . $prefix, null);
                $method->setSetting('publishKey' . $prefix, null);
                \XLite\Core\Database::getEM()->flush();

                \XLite\Core\TopMessage::addWarning(
                    'Your Stripe account is no longer accessible. Please connect with Stripe once again.'
                );
            }
            if ($method->getProcessor() instanceOf \XLite\Module\XC\Stripe\Model\Payment\Stripe && $method->isSettingsConfigured() && !\XLite\Core\Config::getInstance()->Security->customer_security) {
                \XLite\Core\TopMessage::addWarning(
                    'The "Stripe" feature requires https to be properly set up for your store.',
                    [
                        'url' => \XLite\Core\Converter::buildURL('https_settings'),
                    ]
                );
            }
        }
        parent::run();
    }

    /**
     * Update payment method
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $method = $this->getPaymentMethod();
        if ($method->getProcessor() instanceOf \XLite\Module\XC\Stripe\Model\Payment\Stripe) {
            $oldTestValue = $method->getSetting('mode');
        }

        parent::doActionUpdate();

        if ($method->getProcessor() instanceOf \XLite\Module\XC\Stripe\Model\Payment\Stripe) {

            if ($method->isSettingsConfigured() && !\XLite\Core\Config::getInstance()->Security->customer_security) {
                \XLite\Core\TopMessage::addWarning(
                    'The "Stripe" feature requires https to be properly set up for your store.',
                    [
                        'url' => \XLite\Core\Converter::buildURL('https_settings'),
                    ]
                );
            }

            $newTestValue = $method->getSetting('mode');
            $prefix = $method->getProcessor()->isTestMode($method) ? 'Test' : '';
            if ($newTestValue !== $oldTestValue && !$method->getSetting('accessToken' . $prefix)) {
                list($result, $error) = \XLite\Module\XC\Stripe\Core\OAuth::getInstance()->refreshToken($method);

                if (!empty($error)) {
                    \XLite\Core\TopMessage::addError($error);
                    $method->setSetting('mode', $oldTestValue);
                    $this->setReturnURL(
                        \XLite\Core\Converter::buildURL(
                            'payment_method',
                            null,
                            array('method_id' => $method->getMethodId())
                        )
                    );
                }

                \Xlite\Core\Database::getEM()->flush();
            }
        }
    }
}
