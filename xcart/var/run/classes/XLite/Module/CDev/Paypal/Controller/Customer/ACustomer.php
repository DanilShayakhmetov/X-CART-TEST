<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Customer;

use XLite\Model\Payment\BackendTransaction;
use XLite\Module\CDev\Paypal;
use XLite\Module\CDev\Paypal\Model\Payment\Processor\PaypalCommercePlatform as PaypalCommercePlatformProcessor;

/**
 * Abstract customer
 */
abstract class ACustomer extends \XLite\Module\QSL\BraintreeVZ\Controller\Customer\ACustomer implements \XLite\Base\IDecorator
{
    /**
     * Defines the common data for JS
     *
     * @return array
     */
    public function defineCommonJSData()
    {
        $list = parent::defineCommonJSData();

        $method = null;
        if (Paypal\Main::isExpressCheckoutEnabled()) {
            $method = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_EC);

        } elseif (Paypal\Main::isPaypalForMarketplacesEnabled()) {
            $method = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PFM);

        } elseif (Paypal\Main::isPaypalCommercePlatformEnabled()) {
            $method = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PCP);
        }

        if ($method && $processor = $method->getProcessor()) {
            $list['PayPalEnvironment'] = $processor->isTestMode($method) ? 'sandbox' : 'production';

            if (Paypal\Main::isPaypalCommercePlatformEnabled()) {
                $paypalSDKParams = [
                    'client-id' => $method->getSetting('client_id'),
                ];

                //if ($processor->isTestMode($method)) {
                //    $paypalSDKParams['debug'] = 'true';
                //}
                //$paypalSDKParams['buyer-country'] = 'US';

                $paypalSDKParams['currency'] = \XLite::getInstance()->getCurrency()->getCode();

                if ($processor->getInitialTransactionType($method) === BackendTransaction::TRAN_TYPE_SALE) {
                    $paypalSDKParams['intent'] = 'capture';
                } else {
                    $paypalSDKParams['intent'] = 'authorize';
                }

                $disableFunding = [];
                $components = ['buttons', 'funding-eligibility'];
                if ($this instanceof \XLite\Controller\Customer\Checkout) {
                    $paypalSDKParams['commit']      = 'true';
                    $components[] = 'hosted-fields';

                    $clientToken = $processor->generateClientToken();

                    $list['PayPalClientToken'] = $clientToken;

                } else {
                    $paypalSDKParams['commit'] = 'false';
                    $disableFunding[]          = 'card';
                }

                if (Paypal\Main::isPaypalCreditForCommercePlatformEnabled()) {
                    $creditMethod = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PC);
                    if ($creditMethod->getSetting('ppcm_enabled')) {
                        $components[] = 'messages';
                    }

                } else {
                    $disableFunding[] = 'credit';
                }

                if ($disableFunding) {
                    $paypalSDKParams['disable-funding'] = implode(',', $disableFunding);
                }

                if ($components) {
                    $paypalSDKParams['components']  = implode(',', $components);
                }

                $list['PayPalSDKParams']            = http_build_query($paypalSDKParams);
                $list['PayPal3Dsecure']             = $method->getSetting('3d_secure');
                $list['PayPalPartnerAttributionId'] = PaypalCommercePlatformProcessor::BN_CODE;
            }
        }

        return $list;
    }
}
