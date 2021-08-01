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

class PaypalForMarketplacesAPI
{
    /**
     * [partner_id, client_id, secret, bn_code]
     *
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
        return $this->isSelfConfigured()
            && \XLite\Core\Config::getInstance()->Security->customer_security
            && \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('XC\MultiVendor');
    }

    /**
     * @return bool
     */
    public function isSelfConfigured()
    {
        return $this->config['email']
            && $this->config['client_id']
            && $this->config['secret']
            && $this->config['partner_id']
            && $this->config['bn_code'];
    }

    /**
     * @param int    $id
     * @param string $logoUrl
     * @param string $returnUrl
     *
     * @return ReferralData
     */
    public function createReferralData($id, $logoUrl, $returnUrl)
    {
        $config = $this->getConfig();

        $referralData = new ReferralData();

        $customerData = new User();
        $customerData->setCustomerType('MERCHANT');

        $partnerSpecificIdentifier = new PartnerSpecificIdentifier();
        $partnerSpecificIdentifier->setType('TRACKING_ID');
        $partnerSpecificIdentifier->setValue($id);
        $customerData->addPartnerSpecificIdentifier($partnerSpecificIdentifier);
        $referralData->setCustomerData($customerData);

        $capabilities = new Capability();
        $capabilities->setCapability('API_INTEGRATION');

        $integrationDetails = new IntegrationDetails();
        $integrationDetails->setPartnerId($config['partner_id']);

        $restApiIntegration = new RestApiIntegration();
        $restApiIntegration->setIntegrationMethod('PAYPAL');
        $restApiIntegration->setIntegrationType('THIRD_PARTY');
        $integrationDetails->setRestApiIntegration($restApiIntegration);

        $restThirdPartyDetails = new RestThirdPartyDetails();
        $restThirdPartyDetails->setPartnerClientId($config['client_id']);
        $restThirdPartyDetails->addFeature('PAYMENT');
        $restThirdPartyDetails->addFeature('REFUND');
        $restThirdPartyDetails->addFeature('PARTNER_FEE');
        $restThirdPartyDetails->addFeature('DELAY_FUNDS_DISBURSEMENT');
        $integrationDetails->setRestThirdPartyDetails($restThirdPartyDetails);

        $capabilities->setApiIntegrationPreference($integrationDetails);

        $referralData->addRequestedCapability($capabilities);

        $webExperiencePreference = new WebExperiencePreference();
        $webExperiencePreference->setReturnUrl($returnUrl);

        if ($logoUrl) {
            $webExperiencePreference->setPartnerLogoUrl($logoUrl);
        }
        $referralData->setWebExperiencePreference($webExperiencePreference);

        $legalConsent = new LegalConsent();
        $legalConsent->setType('SHARE_DATA_CONSENT');
        $legalConsent->setGranted(true);
        $referralData->addCollectedConsent($legalConsent);

        $referralData->addProduct('EXPRESS_CHECKOUT');

        return $referralData->create($this->getApiContext());
    }

    /**
     * @param string $partnerReferralId
     *
     * @return ReferralData
     */
    public function getReferralData($partnerReferralId)
    {
        return ReferralData::get($partnerReferralId, $this->getApiContext());
    }

    /**
     * @param string $merchantId
     *
     * @return MerchantIntegration
     */
    public function getMerchantIntegration($merchantId)
    {
        $config = $this->getConfig();

        return MerchantIntegration::get($config['partner_id'], $merchantId, $this->getApiContext());
    }

    /**
     * @param \XLite\Model\Payment\Transaction $transaction
     * @param string                           $cancelUrl
     * @param string                           $returnUrl
     * @param string                           $notifyUrl
     *
     * @return Order
     */
    public function createOrder($transaction, $cancelUrl, $returnUrl, $notifyUrl)
    {
        $order = new Order();

        $order->setPurchaseUnits($this->getPurchaseUnits($transaction, $notifyUrl));

        $applicationContext = new ApplicationContext();
        $applicationContext->setBrandName(\XLite\Core\Config::getInstance()->Company->company_name);
        $applicationContext->setShippingPreference('SET_PROVIDED_ADDRESS');
        $applicationContext->setUserAction('COMMIT');
        $order->setApplicationContext($applicationContext);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($returnUrl);
        $redirectUrls->setCancelUrl($cancelUrl);
        $order->setRedirectUrls($redirectUrls);

        return $order->create($this->getApiContext());
    }

    /**
     * @param \XLite\Model\Payment\Transaction $transaction
     * @param string                           $notifyUrl
     *
     * @return PurchaseUnit[]
     */
    protected function getPurchaseUnits($transaction, $notifyUrl)
    {
        $xcartOrder = $transaction->getOrder();

        $config = $this->getConfig();

        $purchaseUnit = new PurchaseUnit();
        $purchaseUnit->setReferenceId($transaction->getPublicId());

        $amount = new Amount();

        $currency     = $xcartOrder->getCurrency();
        $currencyCode = $currency
            ? $currency->getCode()
            : 'USD';
        $amount->setCurrency($currencyCode);

        $total = $xcartOrder->getTotal();
        if ($currency) {
            $total = $currency->roundValue($total);
        }
        $amount->setTotal($total);

        // todo: check for total eq
        $purchaseUnit->setAmount($amount);

        $payee = new Payee();
        $payee->setMerchantId($config['additional_merchant_id']);
        $purchaseUnit->setPayee($payee);

        $purchaseUnit->setCustom($transaction->getPublicId());

        $purchaseUnit->setInvoiceNumber($transaction->getPublicId());

        if ($xcartOrder->isShippable()) {
            $address         = $xcartOrder->getProfile()->getShippingAddress();
            $shippingAddress = new ShippingAddress();
            $shippingAddress->setRecipientName($address->getName());
            $shippingAddress->setLine1($address->getStreet());
            $shippingAddress->setCity($address->getCity());
            $shippingAddress->setState($address->getState() ? $address->getState()->getCode() : '');
            $shippingAddress->setCountryCode($address->getCountryCode());
            $shippingAddress->setPostalCode($address->getZipcode());
            $shippingAddress->setPhone($address->getPhone() ? $this->normalizePhoneNumber($address->getPhone()) : null);
            $purchaseUnit->setShippingAddress($shippingAddress);

            $purchaseUnit->setShippingMethod($xcartOrder->getShippingMethodName());
        }

        $purchaseUnit->setPaymentDescriptor($config['payment_descriptor']);

        return [$purchaseUnit];
    }

    /**
     * @param $phone
     *
     * @return string
     */
    protected function normalizePhoneNumber($phone)
    {
        $result = '';
        $parenthesesOpen = false;
        foreach (str_split(preg_replace('/[^+0-9\s\(\)-\.]/', '', trim($phone))) as $pos => $ch) {
            if ($pos != 0 && $ch == '+'
                || $parenthesesOpen && $ch == '('
                || !$parenthesesOpen && $ch == ')'
            ) {
                continue;
            }
            $parenthesesOpen = $ch == '(' ?: $parenthesesOpen;
            $parenthesesOpen = $ch == ')' ? !($ch == ')') : $parenthesesOpen;

            $result .= $ch;
        }

        return $result;
    }

    /**
     * @param string $orderId
     *
     * @return Order
     */
    public function getOrder($orderId)
    {
        try {
            return Order::get($orderId, $this->getApiContext());

        } catch (PayPalConnectionException $e) {
            return null;
        }
    }

    /**
     * @param string $orderId
     * @param string $disbursementMode
     *
     * @return PayOrderResponse
     */
    public function getPayOrder($orderId, $disbursementMode)
    {
        $payOrderRequest = new PayOrderRequest();
        $payOrderRequest->setDisbursementMode($disbursementMode);

        return $payOrderRequest->pay($orderId, $this->getApiContext());
    }

    /**
     * @param Capture      $capture
     * @param PurchaseUnit $purchaseUnit
     *
     * @return Refund
     * @throws \PayPal\Exception\PayPalConnectionException
     */
    public function refundCapture($capture, $purchaseUnit)
    {
        return $capture->refund(
            $purchaseUnit->getInvoiceNumber(),
            $purchaseUnit->getCustom(),
            $purchaseUnit->getPayee()->getEmail(),
            $this->getApiContext()
        );
    }

    public function getCaptureInfo($orderId)
    {
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
     * @param string $cancelUrl
     *
     * @return ReferencedPayoutsItem
     */
    public function createReferencedPayoutsItem($captureId)
    {
        $referencedPayoutsItem = new ReferencedPayoutsItem();

        $referencedPayoutsItem->setReferenceType('TRANSACTION_ID');
        $referencedPayoutsItem->setReferenceId($captureId);

        return $referencedPayoutsItem->create($this->getApiContext());
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
                new OAuthTokenCredential($config['client_id'], $config['secret'])
            );

            $this->apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', $config['bn_code']);
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

    /**
     * Get amount for 'Refund' transaction
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return float
     */
    public function getRefundAmount($transaction)
    {
        /** @var \XLite\Model\Order $order */
        $paymentTransaction = $transaction instanceOf \XLite\Model\Payment\BackendTransaction
            ? $transaction->getPaymentTransaction()
            : $transaction;

        /** @var \XLite\Model\Currency $currency */
        $currency = $paymentTransaction->getCurrency() ?: $paymentTransaction->getOrder()->getCurrency();

        $amount = $transaction->getValue();

        return $currency->roundValue(max(0, $amount));
    }
}
