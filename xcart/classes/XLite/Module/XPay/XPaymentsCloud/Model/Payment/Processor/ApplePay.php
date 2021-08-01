<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Model\Payment\Processor;

use \XLite\Module\XPay\XPaymentsCloud\Main as ModuleMain;

class ApplePay extends \XLite\Module\XPay\XPaymentsCloud\Model\Payment\Processor\XPaymentsCloud
{
    /**
     * Error codes
     */
    const ERROR_NOT_CONNECTED    = 1;
    const ERROR_WALLETS_DISABLED = 2;
    const ERROR_INVALID_DOMAIN   = 3;
    const ERROR_NO_PAYMENT_CONF  = 4;

    /**
     * List of configuration errors
     *
     * @var array
     */
    protected static $configurationErrors = null;

    /**
     * Flag indicating we need to notify X-Payments about status change
     *
     * @var bool
     */
    protected static $sendStatusChangesByApi = true;

    /**
     * Get payment method admin zone icon URL
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getAdminIconURL(\XLite\Model\Payment\Method $method)
    {
        return \XLite\Core\Layout::getInstance()
                ->getResourceWebPath('modules/XPay/XPaymentsCloud/apple_pay.png');
    }

    /**
     * Get list of configuration errors
     *
     * @return array
     */
    protected function getConfigurationErrors()
    {
        if (null !== static::$configurationErrors) {
            return static::$configurationErrors;
        }

        static::$configurationErrors = array();

        $xpMethod = ModuleMain::getPaymentMethod();

        if (
            !$xpMethod
            || !$xpMethod->getProcessor()->isConfigured($xpMethod)
        ) {

            static::$configurationErrors[] = self::ERROR_NOT_CONNECTED;

        } elseif (
            !\XLite::getController()->isAJAX()
            && !\XLite\Core\Request::getInstance()->isPost()
            && \XLite::isAdminZone()
        ) {

            try {

                $wallets = ModuleMain::getClient()->doGetWallets();

                if (!$wallets->walletsEnabled) {
                    static::$configurationErrors[] = self::ERROR_WALLETS_DISABLED;
                }

                if (!in_array(ModuleMain::getStorefrontDomain(), $wallets->applePay['domains'])) {

                    if (\XLite::getInstance()->getOptions(['service', 'is_cloud'])) {
                        $this->verifyDomain();
                    } else {
                        static::$configurationErrors[] = self::ERROR_INVALID_DOMAIN;
                    }
                }

                if (!$wallets->applePay['processorConfigured']) {
                    static::$configurationErrors[] = self::ERROR_NO_PAYMENT_CONF;
                }

                $xpaymentsStatus = !empty($wallets->applePay['enabled']);
                $currentStatus = ModuleMain::getApplePayMethod()->isEnabled();

                if ($xpaymentsStatus != $currentStatus) {
                    static::$sendStatusChangesByApi = false;
                    ModuleMain::getApplePayMethod()->setEnabled($xpaymentsStatus);
                    static::$sendStatusChangesByApi = true;
                    \XLite\Core\Database::getEM()->flush();
                }

                ModuleMain::getApplePayMethod()->setSetting(
                    'configurationErrors',
                    json_encode(static::$configurationErrors)
                );

                \XLite\Core\Database::getEM()->flush();

            } catch (\XPaymentsCloud\ApiException $exception) {

                $this->handleApiException($exception, 'Unable to communicate with X-Payments');

                static::$configurationErrors = json_decode(ModuleMain::getApplePayMethod()->getSetting('configurationErrors'), true);
            }

        } else {

            static::$configurationErrors = json_decode(ModuleMain::getApplePayMethod()->getSetting('configurationErrors'), true);
        }

        return static::$configurationErrors;
    }

    /**
     * Try to verify domain with Apple Pay
     *
     * @return void
     */
    protected function verifyDomain()
    {
        try {

            $result = ModuleMain::getClient()
                ->doVerifyApplePayDomain(ModuleMain::getStorefrontDomain())
                ->result;

        } catch (\XPaymentsCloud\ApiException $exception) {

            $result = false;
        }

        if (!$result) {
            static::$configurationErrors[] = self::ERROR_INVALID_DOMAIN;
        }
    }

    /**
     * Get translated error message from error code
     *
     * @return string
     */
    protected function getErrorMessage($error)
    {
        $message = '';

        switch ($error) {
            case self::ERROR_NOT_CONNECTED:
                $message = static::t('X-Payments Cloud is not connected');
                break;
            case self::ERROR_WALLETS_DISABLED:
                $message = static::t('Apple Pay is not available for your X-Payments Cloud account');
                break;
            case self::ERROR_INVALID_DOMAIN:
                $message = static::t('Domain is not verified by Apple Pay');
                break;
            case self::ERROR_NO_PAYMENT_CONF:
                $message = static::t('No payment processors which support Apple Pay are enabled');
                break;
            default:
                $message = '';
                break;
        }

        return $message;
    }

    /**
     * Get warning note by payment method
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getWarningNote(\XLite\Model\Payment\Method $method)
    {
        $message = array();

        foreach ($this->getConfigurationErrors() as $error) {
            $message[] = $this->getErrorMessage($error);
        }

        return !empty($message)
            ? implode(' * ', $message)
            : null;
    }

    /**
     * Payment is configured when required keys set and HTTPS enabled
     *
     * @param \XLite\Model\Payment\Method $method
     *
     * @return bool
     */
    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        return empty($this->getConfigurationErrors());
    }

    /**
     * Prevent enabling Apple Pay if main method is disabled
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return boolean
     */
    public function canEnable(\XLite\Model\Payment\Method $method)
    {
        $xpMethod = ModuleMain::getPaymentMethod();
        return parent::canEnable($method)
            && $xpMethod
            && $xpMethod->canEnable();
    }

    /**
     * Get payment method row checkout template
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getCheckoutTemplate(\XLite\Model\Payment\Method $method)
    {
        return 'modules/XPay/XPaymentsCloud/checkout/apple_pay_method.twig';
    }

    /**
     * Send enable Apple Pay request to X-Paymments
     *
     * @param bool $enabled
     *
     * @return void
     */
    protected function enableMethodInXpayments($enabled)
    {
        if (
            static::$sendStatusChangesByApi
            && ModuleMain::getClient()
        ) {

            try {

                $result = ModuleMain::getClient()->doSetApplePayStatus($enabled);

            } catch (\XPaymentsCloud\ApiException $exception) {

                $this->handleApiException($exception, 'Unable to communicate with X-Payments');
            }
        }
    }

    /**
     * If Apple Pay has been enabled but for some reason X-Payments Cloud is disabled,
     * then we need to enable that payment method as well
     *
     * NOTE: We also must disable inherited actions!
     *
     * @return void
     */
    public function enableMethod(\XLite\Model\Payment\Method $method)
    {
        if (
            $method->getEnabled()
            && ModuleMain::getPaymentMethod()
            && !ModuleMain::getPaymentMethod()->getEnabled()
        ) {
            ModuleMain::getPaymentMethod()->setEnabled(true);
        }

        $this->enableMethodInXpayments($method->getEnabled());
    }

    /**
     * Returns the list of settings available for this payment processor
     *
     * @return array
     */
    public function getAvailableSettings()
    {
        return array(
            'configurationErrors',
        );
    }
}
