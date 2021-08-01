<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Admin;

use XLite\Core\Database;
use XLite\Core\Lock\SynchronousTrait;
use XLite\Model\Payment\Method;
use XLite\Module\CDev\Paypal\Core\PaypalCommercePlatform\Onboarding;
use XLite\Module\CDev\Paypal\Main as PaypalMain;
use XLite\Module\CDev\Paypal\Model\Payment\Processor\PaypalCommercePlatform;
use XLite\Module\CDev\Paypal\View\Model\PaypalCommercePlatform as PaypalCommercePlatformModel;
use XLite\Module\CDev\Paypal\View\Settings\MerchantStatusWarning;

class PaypalCommercePlatformSettings extends \XLite\Controller\Admin\AAdmin
{
    use SynchronousTrait;

    /**
     * @var Method
     */
    protected $paymentMethod;

    /**
     * @return string[]
     */
    public static function defineFreeFormIdActions()
    {
        return ['onboarding_return'];
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $paymentMethod = $this->getPaymentMethod();

        return $paymentMethod
            ? $paymentMethod->getName()
            : '';
    }

    /**
     * @return Method
     */
    public function getPaymentMethod()
    {
        if (!isset($this->paymentMethod)) {
            $this->paymentMethod = PaypalMain::getPaymentMethod(
                PaypalMain::PP_METHOD_PCP
            );
        }

        return $this->paymentMethod;
    }

    /**
     * @param null $returnUrl
     *
     * @return string
     *
     * @throws \Exception
     * @see /skins/admin/modules/CDev/Paypal/settings/PaypalCommercePlatform/body.twig
     */
    public function getSignUpUrl($returnUrl = null): string
    {
        \XLite\Module\CDev\Paypal\Core\Lock\PaypalCommerceOnboardingLocker::getInstance()->lock('paypal_onboarding_return');

        $url = \XLite\Core\Cache\ExecuteCached::getCache(['\XLite\Module\CDev\Paypal\View\Settings\PaypalCommercePlatformSettings::getSignUpUrl', $returnUrl]);

        if (empty($url)) {
            $onboarding = new Onboarding();

            $sellerNonce = $this->getSellerNonce();
            $returnUrl   = $returnUrl ?: $this->buildFullURL('paypal_commerce_platform_settings', 'onboarding_return');

            $url = $onboarding->generatePaypalSignUpLink($sellerNonce, $returnUrl);

            if ($url) {
                $url .='&displayMode=minibrowser';

                \XLite\Core\Cache\ExecuteCached::setCache(
                    ['\XLite\Module\CDev\Paypal\View\Settings\PaypalCommercePlatformSettings::getSignUpUrl', $returnUrl],
                    $url,
                    3600
                );
            }
        }

        return $url;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getSellerNonce(): string
    {
        $paymentMethod = $this->getPaymentMethod();
        $sellerNonce   = $paymentMethod->getSetting('sellerNonce');

        if (!$sellerNonce) {
            $sellerNonce = hash('sha512', time());

            $paymentMethod->setSetting('sellerNonce', $sellerNonce);

            Database::getEM()->flush();
        }

        return $sellerNonce;
    }

    public function doActionSetSignUpFlowData()
    {
        \XLite\Module\CDev\Paypal\Core\Lock\PaypalCommerceOnboardingLocker::getInstance()->lock('paypal_onboarding_return');

        $request = \XLite\Core\Request::getInstance();

        if ($request->authCode && $request->sharedId) {
            $paymentMethod = $this->getPaymentMethod();

            $paymentMethod->setSetting('authCode', $request->authCode);
            $paymentMethod->setSetting('client_id', $request->sharedId);
        }

        \XLite\Core\Database::getEM()->flush();

        \XLite\Module\CDev\Paypal\Core\Lock\PaypalCommerceOnboardingLocker::getInstance()->unlock('paypal_onboarding_return');

        $this->setPureAction(true);
    }

    public function doActionOnboardingReturn()
    {
        \XLite\Module\CDev\Paypal\Core\Lock\PaypalCommerceOnboardingLocker::getInstance()->waitForUnlocked('paypal_onboarding_return', null, true);

        $request       = \XLite\Core\Request::getInstance();
        $paymentMethod = $this->getPaymentMethod();

        $accessToken = '';
        $onboarding  = new Onboarding();

        if ($request->merchantIdInPayPal) {
            $paymentMethod->setSetting('merchant_id', $request->merchantIdInPayPal);

            $authCode = $paymentMethod->getSetting('authCode');
            $sharedId = $paymentMethod->getSetting('client_id');

            $sellerNonce = $paymentMethod->getSetting('sellerNonce');

            $accessToken = $onboarding->getSellerAccessToken($sellerNonce, $authCode, $sharedId);
        }

        $credentials = [];
        if ($accessToken) {
            $credentials = $onboarding->getSellerCredentials($accessToken);
        }

        if (isset($credentials['client_id'], $credentials['client_secret'])) {
            $merchantOnboardingStatus = $onboarding->getMerchatOnboardingStatus($request->merchantIdInPayPal, $accessToken);

            if ($merchantOnboardingStatus['payments_receivable']
                && $merchantOnboardingStatus['primary_email_confirmed']
            ) {
                $paymentMethod->setSetting('client_id', $credentials['client_id']);
                $paymentMethod->setSetting('client_secret', $credentials['client_secret']);
                $paymentMethod->setSetting('mode', 'live');

                if ($paymentMethod->isConfigured()) {
                    $paymentMethod->setEnabled(true);
                } elseif ($warningNote = $paymentMethod->getProcessor()->getWarningNote($paymentMethod)) {
                    \XLite\Core\TopMessage::getInstance()->addWarning($warningNote);
                }
            } else {
                $paymentMethod->setSetting('merchant_id','');
                $paymentMethod->setSetting('client_id','');
                $paymentMethod->setSetting('client_secret', '');

                \XLite\Core\TopMessage::getInstance()->addError(new MerchantStatusWarning($merchantOnboardingStatus));
            }
        } else {
            $paymentMethod->setSetting('merchant_id','');
            $paymentMethod->setSetting('client_id','');
            $paymentMethod->setSetting('client_secret', '');

            \XLite\Core\TopMessage::getInstance()->addError(
                'Unfortunately, your API credentials could not be obtained from your PayPal account automatically.'
            );
        }

        $paymentMethod->getProcessor()->enableMethod($paymentMethod);

        \XLite\Core\Database::getEM()->flush();

        $return = $request->return ?? 'paypal_commerce_platform_settings';

        $this->setReturnURL($this->buildURL($return));
    }

    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('modify');
    }

    /**
     * Return class name for the controller main form
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return PaypalCommercePlatformModel::class;
    }
}
