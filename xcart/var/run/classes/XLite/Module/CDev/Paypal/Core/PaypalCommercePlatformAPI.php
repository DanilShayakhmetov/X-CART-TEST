<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core;

use Includes\Utils\FileManager;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Core\PayPalConfigManager;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use Psr\Log\LogLevel;
use XLite\Module\CDev\Paypal\Core\Api\Orders\Amount;
use XLite\Module\CDev\Paypal\Core\Api\Orders\ApplicationContext;
use XLite\Module\CDev\Paypal\Core\Api\Orders\Capture;
use XLite\Module\CDev\Paypal\Core\Api\Orders\Order;
use XLite\Module\CDev\Paypal\Core\Api\Orders\Payee;
use XLite\Module\CDev\Paypal\Core\Api\Orders\PayOrderRequest;
use XLite\Module\CDev\Paypal\Core\Api\Orders\PayOrderResponse;
use XLite\Module\CDev\Paypal\Core\Api\Orders\PurchaseUnit;
use XLite\Module\CDev\Paypal\Core\Api\Orders\RedirectUrls;
use XLite\Module\CDev\Paypal\Core\Api\Orders\ShippingAddress;
use XLite\Module\CDev\Paypal\Core\Api\Payments\Refund;
use XLite\Module\CDev\Paypal\Core\Api\ReferencedPayouts\ReferencedPayoutsItem;
use XLite\Module\CDev\Paypal\Core\Api\Webhooks\EventType;
use XLite\Module\CDev\Paypal\Core\Api\Webhooks\Webhook;
use XLite\Module\CDev\Paypal\Core\Api\Webhooks\WebhooksList;
use XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Capability;
use XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\IntegrationDetails;
use XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\LegalConsent;
use XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\MerchantIntegration;
use XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\PartnerSpecificIdentifier;
use XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\ReferralData;
use XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\RestApiIntegration;
use XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\RestThirdPartyDetails;
use XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\User;
use XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\WebExperiencePreference;
use XLite\Module\CDev\Paypal\Main as PaypalMain;
use XLite\Module\CDev\Paypal\Model\Payment\Processor\PaypalCommercePlatform;

class PaypalCommercePlatformAPI
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var ApiContext
     */
    protected $apiContext;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return bool
     */
    public function isConfigured()
    {
        $expressCheckout = PaypalMain::getPaymentMethod(PaypalMain::PP_METHOD_EC);
        $advanced = PaypalMain::getPaymentMethod(PaypalMain::PP_METHOD_PPA);

        return $this->isSelfConfigured()
            && \XLite\Core\Config::getInstance()->Security->customer_security
            && !$expressCheckout->isEnabled()
            && !$advanced->isEnabled();
    }

    /**
     * @return bool
     */
    public function isSelfConfigured()
    {
        return $this->config['client_id']
            && $this->config['client_secret'];
    }

    public function getWebhooks()
    {
        return WebhooksList::get('APPLICATION', $this->getApiContext());
    }

    public function createWebhook($url, $events)
    {
        $webhook = new Webhook();
        $webhook->setUrl($url);

        foreach ($events as $event) {
            $eventType = new EventType();
            $eventType->setName($event);
            $webhook->addEventType($eventType);
        }

        return $webhook->create($this->getApiContext());
    }

    public function deleteWebhook($webhookId)
    {
        Webhook::delete($webhookId, $this->getApiContext());
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return ApiContext
     */
    public function getApiContext()
    {
        if ($this->apiContext === null) {
            OAuthTokenCredential::$CACHE_PATH = LC_DIR_DATA . 'paypal.auth.cache';

            $config = $this->getConfig();

            $paypalConfig = PayPalConfigManager::getInstance();
            $paypalConfig->addConfigs([
                'cache.enabled'        => true,
                'cache.FileName'       => LC_DIR_DATA . 'paypal.auth.cache',
                'log.LogEnabled'       => true,
                'log.FileName'         => LC_DIR_LOG . date('Y') . LC_DS . date('m') . LC_DS . 'paypal_api.log.' . date('Y-m-d') . '.php',
                'log.LogLevel'         => LogLevel::DEBUG,
                'mode'                 => $config['mode'],
                'http.CURLOPT_TIMEOUT' => 30,
            ]);

            $this->apiContext = new ApiContext(
                new OAuthTokenCredential($config['client_id'], $config['client_secret'])
            );

            $this->apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', PaypalCommercePlatform::BN_CODE);
        }

        return $this->apiContext;
    }

    /**
     * @return bool
     */
    public static function dropPayPalTokenCash()
    {
        return FileManager::deleteFile(LC_DIR_DATA . 'paypal.auth.cache');
    }
}
