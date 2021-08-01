<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\FormField\Input;

class AdditionalAccount extends \XLite\View\FormField\AFormField
{
    const FIELD_TYPE_ADDITIONAL_ACCOUNT = 'additional_account';

    /**
     * @return bool
     */
    public function isVisible()
    {
        return parent::isVisible() && $this->isConnectedOnboardingAvailable();
    }

    /**
     * @return string
     */
    public function isConnectedOnboardingAvailable()
    {
        $method = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(
            \XLite\Module\CDev\Paypal\Main::PP_METHOD_PFM
        );

        return $method->getSetting('email')
            && $method->getSetting('client_id')
            && $method->getSetting('secret')
            && $method->getSetting('partner_id')
            && $method->getSetting('bn_code');
    }

    /**
     * @return string
     */
    public function getConnectionURL()
    {
        $method = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(
            \XLite\Module\CDev\Paypal\Main::PP_METHOD_PFM
        );

        $paypalForMarketplacesAPI = new \XLite\Module\CDev\Paypal\Core\PaypalForMarketplacesAPI([
            'client_id'  => $method->getSetting('client_id'),
            'secret'     => $method->getSetting('secret'),
            'partner_id' => $method->getSetting('partner_id'),
            'bn_code'    => $method->getSetting('bn_code'),
            'mode'       => $method->getProcessor()->isTestMode($method) ? 'sandbox' : 'live',
        ]);

        try {
            if ($method->getSetting('additional_parther_referral_id')) {
                $referralData = $paypalForMarketplacesAPI->getReferralData(
                    $method->getSetting('additional_parther_referral_id')
                );

            } else {
                $logoUrl = \XLite\Core\Config::getInstance()->Security->admin_security
                    ? \XLite::getInstance()->getShopURL(\XLite\Core\Layout::getInstance()->getLogo(), true)
                    : null;

                $referralData = $paypalForMarketplacesAPI->createReferralData(
                    0,
                    $logoUrl,
                    $this->buildFullURL('paypal_settings', '', ['method_id' => $method->getMethodId()])
                );

                if (preg_match('/([^\/]+)$/', $referralData->getLink('self'), $matches)) {
                    $method->setSetting('additional_partner_referral_id', $matches[1]);
                    \XLite\Core\Database::getEM()->flush();
                }
            }

            return $referralData->getLink('action_url');

        } catch (\Exception $e) {

            return '';
        }
    }

    /**
     * @return string
     */
    public function getJSLibURL()
    {
        $method = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(
            \XLite\Module\CDev\Paypal\Main::PP_METHOD_PFM
        );

        return $method->getProcessor()->isTestMode($method)
            ? 'https://www.sandbox.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js'
            : 'https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js';
    }

    /**
     * @return string
     */
    public function isConnected()
    {
        return $this->getMerchantId();
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        $method = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(
            \XLite\Module\CDev\Paypal\Main::PP_METHOD_PFM
        );

        return $method->getSetting('additional_merchant_id');
    }

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return static::FIELD_TYPE_ADDITIONAL_ACCOUNT;
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/Paypal/form_field';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'additional_email.twig';
    }
}
