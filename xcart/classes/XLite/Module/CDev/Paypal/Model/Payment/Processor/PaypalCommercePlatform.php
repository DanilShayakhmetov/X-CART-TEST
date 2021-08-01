<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Model\Payment\Processor;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Orders\OrdersAuthorizeRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Orders\OrdersPatchRequest;
use PayPalCheckoutSdk\Payments\AuthorizationsCaptureRequest;
use PayPalCheckoutSdk\Payments\AuthorizationsVoidRequest;
use PayPalCheckoutSdk\Payments\CapturesRefundRequest;
use XLite\Core\Converter;
use XLite\Core\TopMessage;
use XLite\Model\Payment\BackendTransaction;
use XLite\Model\Payment\Method;
use XLite\Model\Payment\Transaction;
use XLite\Module\CDev\Paypal\Core\PaypalCommercePlatform\Convert\CreateOrder;
use XLite\Module\CDev\Paypal\Core\PaypalCommercePlatform\GenerateClientToken;
use XLite\Module\CDev\Paypal\Core\PaypalCommercePlatform\PaypalClient;
use XLite\Module\CDev\Paypal\Core\PaypalCommercePlatform\Webhook;
use XLite\Module\CDev\Paypal\Core\PaypalCommercePlatformAPI;
use XLite\Module\CDev\Paypal\Main as PaypalMain;
use XLite\View\FormField\Select\TestLiveMode;

class PaypalCommercePlatform extends \XLite\Model\Payment\Base\Online
{
    public const BN_CODE = 'XCart_SP_PCP';

    /**
     * @var PayPalHttpClient
     */
    protected $client;

    /**
     * @var Webhook
     */
    protected $webhook;

    /**
     * @var PaypalCommercePlatformAPI
     */
    protected $api;

    public function __construct()
    {
        $method = PaypalMain::getPaymentMethod(
            PaypalMain::PP_METHOD_PCP
        );

        $client = new PaypalClient(
            $method->getSetting('client_id'),
            $method->getSetting('client_secret'),
            $this->isTestMode($method)
        );

        $this->client = $client->getClient();

        $this->webhook = new Webhook();

        $this->api = new PaypalCommercePlatformAPI([
            'client_id'     => $method->getSetting('client_id'),
            'client_secret' => $method->getSetting('client_secret'),
            'mode'          => $method->getSetting('mode') === TestLiveMode::TEST ? 'sandbox' : 'live',
        ]);
    }

    /**
     * @param string $message
     * @param mixed  $data
     */
    protected static function addLog($message = null, $data = null): void
    {
        $logLevel = \XLite::getInstance()->getOptions(['log_details', 'level']);
        if (intval($logLevel) >= LOG_DEBUG) {
            PaypalMain::addLog('PaypalCommercePlatform Payment:' . $message, $data);
        }
    }

    /**
     * Get payment method admin zone icon URL
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getAdminIconURL(\XLite\Model\Payment\Method $method)
    {
        return true;
    }

    /**
     * Get payment method configuration page URL
     *
     * @param Method  $method                        Payment method
     * @param boolean $justAdded                     Flag if the method is just added via administration panel.
     *                                               Additional init configuration can be provided OPTIONAL
     *
     * @return string
     */
    public function getConfigurationURL(Method $method, $justAdded = false)
    {
        return Converter::buildURL('paypal_commerce_platform_settings');
    }

    /**
     * Get input template
     *
     * @return string
     */
    public function getInputTemplate()
    {
        return 'modules/CDev/Paypal/checkout/paypal_commerce_platform_checkout_box.twig';
    }

    /**
     * Get allowed backend transactions
     *
     * @return string[] Statuses
     */
    public function getAllowedTransactions()
    {
        return [
            BackendTransaction::TRAN_TYPE_CAPTURE,
            BackendTransaction::TRAN_TYPE_CAPTURE_PART,
            BackendTransaction::TRAN_TYPE_VOID,
            BackendTransaction::TRAN_TYPE_REFUND,
            BackendTransaction::TRAN_TYPE_REFUND_PART,
            BackendTransaction::TRAN_TYPE_REFUND_MULTI,
        ];
    }

    /**
     * Get initial transaction type (used when customer places order)
     *
     * @param Method $method Payment method object OPTIONAL
     *
     * @return string
     */
    public function getInitialTransactionType($method = null)
    {
        $transactionType = $method
            ? $method->getSetting('transaction_type')
            : $this->getSetting('transaction_type');

        return $transactionType === 'A'
            ? BackendTransaction::TRAN_TYPE_AUTH
            : BackendTransaction::TRAN_TYPE_SALE;
    }

    /**
     * Check - payment method is configured or not
     *
     * @param Method $method Payment method
     *
     * @return boolean
     */
    public function isConfigured(Method $method)
    {
        return $this->api->isConfigured();
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
        switch ($this->getNotSwitchableReasonType($method)) {
            case 'merchant_status':
                return static::t(
                    'Account is not well configured on PayPal side'
                );
            case 'conflict':
                return static::t(
                    'PayPal checkout and PayPal express checkout (legacy) / PayPal Payments Advanced are not able to work together.'
                );
                break;
            default:
                return parent::getWarningNote($method);
        }
    }

    /**
     * @param \XLite\Model\Payment\Method $method
     *
     * @return string
     */
    public function getNotSwitchableReasonType(\XLite\Model\Payment\Method $method)
    {
        if (\XLite\Module\CDev\Paypal\Main::PP_METHOD_PCP === $method->getServiceName()
            && !$this->isConfigured($method)
            && $this->api->isSelfConfigured()
        ) {
            $expressCheckout = PaypalMain::getPaymentMethod(PaypalMain::PP_METHOD_EC);
            $advanced        = PaypalMain::getPaymentMethod(PaypalMain::PP_METHOD_PPA);

            if (!\XLite\Core\Config::getInstance()->Security->customer_security) {
                return 'https';

            } elseif ($expressCheckout->isEnabled() || $advanced->isEnabled()) {
                return 'conflict';
            }
        }

        return '';
    }

    /**
     * Do something when payment method is enabled or disabled
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return void
     */
    public function enableMethod(\XLite\Model\Payment\Method $method)
    {
        parent::enableMethod($method);

        if ($method->getEnabled()) {
            try {
                $webhooks = $this->api->getWebhooks();
                foreach ($webhooks->getWebhooks() as $webhook) {
                    $this->api->deleteWebhook($webhook->getId());
                }

                $this->api->createWebhook(
                    $this->getCallbackURL(),
                    [
                        //'CHECKOUT.ORDER.APPROVED',
                        //'CHECKOUT.ORDER.COMPLETED',
                        'PAYMENT.AUTHORIZATION.VOIDED',
                        'PAYMENT.CAPTURE.COMPLETED',
                        'PAYMENT.CAPTURE.DENIED',
                        'PAYMENT.CAPTURE.REFUNDED',
                    ]
                );
            } catch (\Exception $e) {
                TopMessage::addWarning('Webhook listener error: could not subscribe to event(s).');
            }

        } else {
            try {
                $webhooks = $this->api->getWebhooks();
                foreach ($webhooks->getWebhooks() as $webhook) {
                    $this->api->deleteWebhook($webhook->getId());
                }
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * @return string
     */
    public function generateClientToken()
    {
        try {
            $request = new GenerateClientToken();
            $result = $this->client->execute($request);

            return $result->result->client_token;

        } catch (\Exception $e) {
            $message = $e->getMessage();
            $message = @json_decode($message, true);

            static::addLog('generateToken error', $message);
        }

        return '';
    }

    /**
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_create
     * @see https://developer.paypal.com/docs/checkout/reference/server-integration/set-up-transaction/#on-the-server
     *
     * @param Transaction $transaction
     *
     * @return mixed|\PayPalHttp\HttpResponse
     */
    public function createOrder(Transaction $transaction)
    {
        $this->transaction = $transaction;

        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->payPalPartnerAttributionId(static::BN_CODE);

        // @see https://developer.paypal.com/docs/api/orders/v2/#errors
        //$request->headers['PayPal-Mock-Response'] = json_encode([
        //    'mock_application_codes' => 'INVALID_CURRENCY_CODE'
        //]);

        $order = $transaction->getOrder();

        $applicationContext = [];

        //$applicationContext = [
        //    //'brand_name' ,
        //    //'locale',
        //    'shipping_preference' => $order->isShippable() ? 'SET_PROVIDED_ADDRESS' : 'NO_SHIPPING',
        //];

        if (!$order->isShippable()) {
            $applicationContext['shipping_preference'] = 'NO_SHIPPING';
        } elseif ($transaction->getProfile() && $transaction->getProfile()->getShippingAddress()) {
            $applicationContext['shipping_preference'] = 'SET_PROVIDED_ADDRESS';
        }

        $converter = new CreateOrder();
        $orderData = $converter->fromTransaction($transaction, $applicationContext);

        $request->body = $orderData;

        $response = [];
        try {
            static::addLog('createOrder request', $orderData);

            $response = $this->client->execute($request);

            static::addLog('createOrder response', $response);

            if ($response->statusCode == 201) {
                $orderId = $response->result->id;

                $this->transaction->setDataCell('PaypalOrderId', $orderId, 'PaypalOrderId', 'C');
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $message = @json_decode($message, true);

            static::addLog('createOrder error', $message);

            $response = $message;
        }

        return $response;
    }

    /**
     * @param string $orderId
     *
     * @return array
     */
    public function getPaypalOrder($orderId)
    {
        $request = new OrdersGetRequest($orderId);

        $response = [];
        try {
            static::addLog('getOrder request', $orderId);

            $response = $this->client->execute($request);

            $response = $response->result;

            static::addLog('getOrder response', json_encode($response));

        } catch (\Exception $e) {
            $message = $e->getMessage();
            $message = @json_decode($message, true) ?: ['message' => 'Unexpected error'];

            static::addLog('getOrder error', $message);
        }

        return $response;
    }

    /**
     * @param Transaction $transaction
     * @param array       $data
     */
    public function onApprove(Transaction $transaction, $data)
    {
        static::addLog('onApprove', $data);

        $this->transaction = $transaction;

        if (isset($data['payerID'])) {
            $this->transaction->setDataCell('PaypalPayerId', $data['payerID'], 'Paypal Payer ID', 'C');
        }

        if (isset($data['card'])) {
            $this->transaction->setDataCell('PaypalCardType', $data['card']['card_type'], 'Paypal Card type', 'C');
            $this->transaction->setDataCell('PaypalCardLastDigits', $data['card']['last_digits'], 'Paypal Card last digits', 'C');
        }
    }

    /**
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_patch
     *
     * @param Transaction $transaction
     *
     * @return array|mixed|string|string[]
     */
    public function patchOrder(Transaction $transaction)
    {
        $orderId = $transaction->getDataCell('PaypalOrderId')->getValue();

        $request = new OrdersPatchRequest($orderId);

        $request->headers['PayPal-Partner-Attribution-Id'] = static::BN_CODE;

        $converter = new CreateOrder();
        $orderData = $converter->fromTransaction($transaction);

        $operation = [
            'op'    => 'replace',
            'path'  => "/purchase_units/@reference_id=='{$transaction->getPublicId()}'",
            'value' => $orderData['purchase_units'][0],
        ];

        $request->body = [$operation];

        // @see https://developer.paypal.com/docs/api/orders/v2/#errors
        //$request->headers['PayPal-Mock-Response'] = json_encode([
        //    'mock_application_codes' => 'CANNOT_BE_ZERO_OR_NEGATIVE',
        //]);

        try {
            static::addLog('patchOrder request', $operation);

            $response = $this->client->execute($request);

            $response = $response->result;

            static::addLog('patchOrder response', $response);

        } catch (\Exception $e) {
            $message = $e->getMessage();
            $message = @json_decode($message, true) ?: ['message' => 'Unexpected error'];

            static::addLog('patchOrder error', $message);

            $response = $message;
        }

        return $response;
    }

    /**
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_capture
     * @see https://developer.paypal.com/docs/checkout/reference/server-integration/capture-transaction/
     *
     * @param Transaction $transaction
     *
     * @return array|mixed|string[]
     */
    public function captureTransaction(Transaction $transaction)
    {
        $orderId = $transaction->getDataCell('PaypalOrderId')->getValue();

        $request = new OrdersCaptureRequest($orderId);
        $request->prefer('return=representation');
        $request->headers['PayPal-Partner-Attribution-Id'] = static::BN_CODE;

        // @see https://developer.paypal.com/docs/api/orders/v2/#errors
        //$request->headers['PayPal-Mock-Response'] = json_encode([
        //    'mock_application_codes' => 'ACTION_DOES_NOT_MATCH_INTENT',
        //]);

        try {
            static::addLog('captureTransaction request');

            $response = $this->client->execute($request);

            static::addLog('captureTransaction response', $response);

            $status = $response->result->status ?? 'ERROR';

            $captures = [];
            foreach ($response->result->purchase_units ?? [] as $purchase_unit) {
                foreach ($purchase_unit->payments->captures ?? [] as $capture) {
                    $captures[] = $capture->id;
                }
            }

            return [
                'status'   => $status,
                'captures' => $captures,
            ];
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $message = @json_decode($message, true) ?: [];

            static::addLog('captureTransaction error', $message);

            return $message;
        }
    }

    /**
     * @see https://developer.paypal.com/docs/api/orders/v2/#orders_authorize
     * @see https://developer.paypal.com/docs/checkout/reference/server-integration/authorize-transaction/
     *
     * @param Transaction $transaction
     *
     * @return array|mixed|string[]
     */
    public function createAuthorization(Transaction $transaction)
    {
        $orderId = $transaction->getDataCell('PaypalOrderId')->getValue();

        $request = new OrdersAuthorizeRequest($orderId);
        $request->prefer('return=representation');
        $request->headers['PayPal-Partner-Attribution-Id'] = static::BN_CODE;

        // @see https://developer.paypal.com/docs/api/orders/v2/#errors
        //$request->headers['PayPal-Mock-Response'] = json_encode([
        //    'mock_application_codes' => 'ACTION_DOES_NOT_MATCH_INTENT',
        //]);

        try {
            static::addLog('createAuthorization request');

            $response = $this->client->execute($request);

            static::addLog('createAuthorization response', $response);

            $status = $response->result->status ?? 'ERROR';

            $authorizations = [];
            foreach ($response->result->purchase_units ?? [] as $purchase_unit) {
                foreach ($purchase_unit->payments->authorizations ?? [] as $authorization) {
                    $authorizations[] = $authorization->id;
                }
            }

            return [
                'status'         => $status,
                'authorizations' => $authorizations,
            ];
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $message = @json_decode($message, true) ?: [];

            static::addLog('createAuthorization error', $message);

            return $message;
        }
    }

    /**
     * Get callback owner transaction or null
     *
     * @return Transaction
     */
    public function getCallbackOwnerTransaction()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if ($data && $this->webhook->isWebhookData($data)) {
            static::addLog('getCallbackOwnerTransaction', $data);

            return $this->webhook->detectTransaction($data);
        }

        return null;
    }

    /**
     * @param Transaction $transaction Callback-owner transaction
     */
    public function processCallback(Transaction $transaction)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $this->transaction = $transaction;
        $this->logCallback((array) $data);

        static::addLog('processCallback', $data);

        $currency        = $transaction->getCurrency();
        $transactionSums = $this->getTransactionSums($transaction);

        if ($data['event_type'] === 'PAYMENT.CAPTURE.COMPLETED'
            || $data['event_type'] === 'PAYMENT.CAPTURE.DENIED'
        ) {
            $capture   = $data['resource'];
            $captureId = $capture['id'];

            $backendTransaction = $this->getBackendTransactionByCaptureId($transaction, $captureId);

            if (!$backendTransaction) {
                $amountValue = $currency->roundValue($capture['amount']['value']);

                if ($amountValue === $transactionSums['authorized']) {
                    $type = BackendTransaction::TRAN_TYPE_CAPTURE;
                } else {
                    $type = BackendTransaction::TRAN_TYPE_CAPTURE_PART;
                }

                $backendTransaction = $transaction->createBackendTransaction($type);
                $backendTransaction->setDataCell('PaypalCaptureID', $captureId, 'Paypal Capture Id');
                $backendTransaction->setValue($amountValue);
            }

            $transactionStatus = Transaction::STATUS_FAILED;
            if ($capture['status'] === 'COMPLETED') {
                $transactionStatus = Transaction::STATUS_SUCCESS;
            } elseif ($capture['status'] !== 'VOIDED') {
                $transactionStatus = Transaction::STATUS_PENDING;
            }

            $backendTransaction->setStatus($transactionStatus);
            $backendTransaction->registerTransactionInOrderHistory('callback, webhook');

        } elseif ($data['event_type'] === 'PAYMENT.AUTHORIZATION.VOIDED') {
            $authorisation   = $data['resource'];
            $authorisationId = $authorisation['id'];

            $backendTransaction = $this->getBackendTransactionByAuthorizationId($transaction, $authorisationId, BackendTransaction::TRAN_TYPE_VOID);

            if (!$backendTransaction) {
                $amountValue = $currency->roundValue($authorisation['amount']['value']);

                $backendTransaction = $transaction->createBackendTransaction(BackendTransaction::TRAN_TYPE_VOID);
                $backendTransaction->setDataCell('PaypalAuthorizationID', $authorisationId, 'Paypal Authorization ID');
                $backendTransaction->setValue($amountValue);
            }

            $transactionStatus = Transaction::STATUS_SUCCESS;

            $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_VOID);
            $backendTransaction->setStatus($transactionStatus);
            $backendTransaction->registerTransactionInOrderHistory('callback, webhook');
        } elseif ($data['event_type'] === 'PAYMENT.CAPTURE.REFUNDED') {
            $refund   = $data['resource'];
            $refundId = $refund['id'];

            $backendTransaction = $this->getBackendTransactionByRefundId($transaction, $refundId);

            if (!$backendTransaction) {
                $amountValue = $currency->roundValue($refund['amount']['value']);

                if ($amountValue === $transactionSums['captured']) {
                    $type = BackendTransaction::TRAN_TYPE_REFUND;
                } else {
                    $type = BackendTransaction::TRAN_TYPE_REFUND_MULTI;
                }

                $backendTransaction = $transaction->createBackendTransaction($type);
                $backendTransaction->setDataCell('PaypalRefundID', $refundId, 'Paypal Refund Id');
                $backendTransaction->setValue($amountValue);
            }

            $transactionStatus = Transaction::STATUS_FAILED;
            if ($refund['status'] === 'COMPLETED') {
                $transactionStatus = Transaction::STATUS_SUCCESS;
            } elseif ($refund['status'] !== 'VOIDED') {
                $transactionStatus = Transaction::STATUS_PENDING;
            }

            $backendTransaction->setStatus($transactionStatus);
            $backendTransaction->registerTransactionInOrderHistory('callback, webhook');
        }
    }

    /**
     * Get allowed currencies
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return array
     */
    protected function getAllowedCurrencies(\XLite\Model\Payment\Method $method)
    {
        return [
            'AUD',
            'BRL',
            'CAD',
            'CZK',
            'DKK',
            'EUR',
            'HKD',
            'HUF',
            'INR',
            'ILS',
            'JPY',
            'MYR',
            'MXN',
            'TWD',
            'NZD',
            'NOK',
            'PHP',
            'PLN',
            'GBP',
            'RUB',
            'SGD',
            'SEK',
            'CHF',
            'THB',
            'USD',
        ];
    }

    /**
     * @return string
     */
    protected function doInitialPayment()
    {
        $transaction = $this->transaction;

        $onApproveData = \XLite\Core\Request::getInstance()->pcp_on_approve_data;
        if ($onApproveData) {
            $this->onApprove($transaction, json_decode($onApproveData, true));
        }

        $initialTransactionType = $this->getInitialTransactionType();
        $backendTransaction     = $transaction->createBackendTransaction($initialTransactionType);

        $patchResponse = $this->patchOrder($transaction);

        $status            = self::FAILED;
        $transactionStatus = Transaction::STATUS_FAILED;

        if (empty($patchResponse)) {
            if ($initialTransactionType === BackendTransaction::TRAN_TYPE_SALE) {
                $response = $this->captureTransaction($transaction);

                if ($response['status']) {
                    $this->setDetail('status', $response['status'], 'Status');
                    foreach ($response['captures'] as $captureId) {
                        $transaction->setDataCell('PaypalCaptureID', $captureId, 'Paypal Capture Id');
                        $backendTransaction->setDataCell('PaypalCaptureID', $captureId, 'Paypal Capture Id');
                        break;
                    }
                }
            } else {
                $response = $this->createAuthorization($transaction);

                if ($response['status']) {
                    $this->setDetail('status', $response['status'], 'Status');
                    foreach ($response['authorizations'] as $authorizationId) {
                        $transaction->setDataCell('PaypalAuthorizationID', $authorizationId, 'Paypal Authorization ID');
                        $backendTransaction->setDataCell('PaypalAuthorizationID', $authorizationId, 'Paypal Authorization ID');
                        break;
                    }
                }
            }

            if ($response['status']) {
                if ($response['status'] === 'COMPLETED') {
                    $transactionStatus = $transaction::STATUS_SUCCESS;
                    $status            = self::COMPLETED;
                } elseif ($response['status'] !== 'VOIDED') {
                    $transactionStatus = $transaction::STATUS_PENDING;
                    $status            = self::PENDING;
                }
            } else {
                $message = '';
                if (isset($response['details'])) {
                    foreach ($response['details'] as $detail) {
                        $message .= $detail['description'] . '; ';
                    }
                } elseif (isset($response['message'])) {
                    $message = $response['message'];
                } else {
                    $message = 'Your payment could not be processed at this time. Please make sure the card information was entered correctly and resubmit. If the problem persists, please contact your credit card company to authorize the purchase.';
                }

                $this->setDetail(
                    'status',
                    'Failed: ' . $message,
                    'Status'
                );

                $transaction->setNote($message);
            }
        } else {
            $this->setDetail(
                'status',
                'Failed: ' . $patchResponse['message'] ?? 'Unexpected error',
                'Status'
            );

            $transaction->setNote($patchResponse['message'] ?? 'Your payment could not be processed at this time. Please make sure the card information was entered correctly and resubmit. If the problem persists, please contact your credit card company to authorize the purchase.');
        }

        $transaction->setStatus($transactionStatus);
        $backendTransaction->setStatus($transactionStatus);

        return $status;
    }

    /**
     * @see https://developer.paypal.com/docs/api/payments/v2/#authorizations_capture
     * @see https://developer.paypal.com/docs/checkout/reference/server-integration/capture-authorization/
     *
     * @param BackendTransaction $backendTransaction Trandaction
     *
     * @return bool
     */
    protected function doCapture(BackendTransaction $backendTransaction)
    {
        $result = false;
        $status = Transaction::STATUS_FAILED;

        $transaction = $backendTransaction->getPaymentTransaction();

        $authorizationID = $transaction->getDataCell('PaypalAuthorizationID');
        if ($authorizationID) {
            $request = new AuthorizationsCaptureRequest($authorizationID->getValue());
            $request->prefer('return=representation');
            $request->headers['PayPal-Partner-Attribution-Id'] = static::BN_CODE;

            $amount = $backendTransaction->getValue();

            $authorizedAmount = 0;
            $capturedAmount   = 0;

            $request->data = [
                'final_capture' => true,
            ];

            // @see https://developer.paypal.com/docs/api/payments/v2/#errors
            //$request->headers['PayPal-Mock-Response'] = json_encode([
            //    'mock_application_codes' => 'ACTION_DOES_NOT_MATCH_INTENT',
            //]);

            try {
                static::addLog('doCapture request');

                $response = $this->client->execute($request);

                static::addLog('doCapture response', $response);

                $transaction->setDataCell('PaypalCaptureID', $response->result->id, 'Paypal Capture Id');
                $backendTransaction->setDataCell('PaypalCaptureID', $response->result->id, 'Paypal Capture Id');

                // process capture data
                if ($response->result->status === 'COMPLETED') {
                    $result = true;
                    $status = Transaction::STATUS_SUCCESS;

                    \XLite\Core\TopMessage::getInstance()->addInfo('Payment has been captured successfully');
                } elseif ($response->result->status === 'PENDING') {
                    $backendTransaction->setDataCell('pendingReason', $response->result->status_details->reason, 'Pending Reason');

                    $result = true;
                    $status = Transaction::STATUS_PENDING;

                    \XLite\Core\TopMessage::getInstance()->addInfo('Payment has been captured successfully (pending)');

                } else {
                    \XLite\Core\TopMessage::getInstance()
                        ->addError('Transaction failure. PayPal response: ' . $response->result->status_details->reason);

                }

            } catch (\Exception $e) {
                $message = $e->getMessage();
                $message = @json_decode($message, true) ?: ['message' => 'Unexpected error'];

                static::addLog('doCapture error', $message);

                $details = '';
                foreach ($message['details'] ?? [] as $detail) {
                    $details .= $detail['description'] . '; ';
                }

                \XLite\Core\TopMessage::getInstance()
                    ->addError("Transaction failure. PayPal response: {$message['message']} ({$details})");
            }

            $backendTransaction->setStatus($status);
            $backendTransaction->update();
        }

        return $result;
    }

    /**
     * @param BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doCapturePart(BackendTransaction $transaction)
    {
        return $this->doCapture($transaction);
    }

    /**
     * @param BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doCaptureMulti(BackendTransaction $transaction)
    {
        return $this->doCapture($transaction);
    }

    /**
     * @see https://developer.paypal.com/docs/api/payments/v2/#authorizations_void
     *
     * @param BackendTransaction $backendTransaction Trandaction
     *
     * @return bool
     */
    protected function doVoid(BackendTransaction $backendTransaction)
    {
        $result = false;
        $status = Transaction::STATUS_FAILED;

        $transaction = $backendTransaction->getPaymentTransaction();

        $authorizationID = $transaction->getDataCell('PaypalAuthorizationID');
        if ($authorizationID) {
            $request = new AuthorizationsVoidRequest($authorizationID->getValue());

            $request->headers['PayPal-Partner-Attribution-Id'] = static::BN_CODE;

            // @see https://developer.paypal.com/docs/api/payments/v2/#errors
            //$request->headers['PayPal-Mock-Response'] = json_encode([
            //    'mock_application_codes' => 'ACTION_DOES_NOT_MATCH_INTENT',
            //]);

            try {
                static::addLog('doVoid request');

                $response = $this->client->execute($request);

                static::addLog('doVoid response', $response);

                $backendTransaction->setDataCell('PaypalAuthorizationID', $authorizationID->getValue(), 'Paypal Authorization ID');

                if ($response->statusCode === 204) {
                    $result = true;
                    $status = Transaction::STATUS_SUCCESS;

                    $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_VOID);

                    \XLite\Core\TopMessage::getInstance()->addInfo('Payment has been voided successfully');
                } else {
                    \XLite\Core\TopMessage::getInstance()
                        ->addError('Transaction failure.');
                }

            } catch (\Exception $e) {
                $message = $e->getMessage();
                $message = @json_decode($message, true) ?: ['message' => 'Unexpected error'];

                static::addLog('doVoid error', $message);

                $details = '';
                foreach ($message['details'] ?? [] as $detail) {
                    $details .= $detail['description'] . '; ';
                }

                \XLite\Core\TopMessage::getInstance()
                    ->addError("Transaction failure. PayPal response: {$message['message']} ({$details})");
            }

            $backendTransaction->setStatus($status);
            $backendTransaction->update();
        }

        return $result;
    }

    /**
     * @see https://developer.paypal.com/docs/api/payments/v2/#captures_refund
     *
     * @param BackendTransaction $backendTransaction Transaction
     *
     * @return boolean
     */
    protected function doRefund(BackendTransaction $backendTransaction)
    {
        $result = false;
        $status = Transaction::STATUS_FAILED;

        $transaction = $backendTransaction->getPaymentTransaction();

        $captureID = $transaction->getDataCell('PaypalCaptureID');
        if ($captureID) {
            $currency = $transaction->getCurrency();

            $requestData = [
                'amount' => [
                    'currency_code' => $currency->getCode(),
                    'value'         => $currency->roundValue($backendTransaction->getValue()),
                ],
            ];

            $request = new CapturesRefundRequest($captureID->getValue());
            $request->prefer('return=representation');
            $request->headers['PayPal-Partner-Attribution-Id'] = static::BN_CODE;

            $request->body = $requestData;

            // @see https://developer.paypal.com/docs/api/payments/v2/#errors
            //$request->headers['PayPal-Mock-Response'] = json_encode([
            //    'mock_application_codes' => 'ACTION_DOES_NOT_MATCH_INTENT',
            //]);

            try {
                static::addLog('doRefund request', $requestData);

                $response = $this->client->execute($request);

                static::addLog('doRefund response', $response);

                $backendTransaction->setDataCell('PaypalRefundID', $response->result->id, 'Paypal Refund ID');

                if ($response->statusCode === 201) {
                    $result = true;
                    $status = Transaction::STATUS_SUCCESS;

                    \XLite\Core\TopMessage::getInstance()->addInfo('Payment has been refunded successfully');
                } else {
                    \XLite\Core\TopMessage::getInstance()
                        ->addError('Transaction failure.');
                }

            } catch (\Exception $e) {
                $message = $e->getMessage();
                $message = @json_decode($message, true) ?: ['message' => 'Unexpected error'];

                static::addLog('doRefund error', $message);

                $details = '';
                foreach ($message['details'] ?? [] as $detail) {
                    $details .= $detail['description'] . '; ';
                }

                \XLite\Core\TopMessage::getInstance()
                    ->addError("Transaction failure. PayPal response: {$message['message']} ({$details})");
            }

            $backendTransaction->setStatus($status);
            $backendTransaction->update();
        }

        return $result;
    }

    /**
     * @param BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doRefundPart(BackendTransaction $transaction)
    {
        return $this->doRefund($transaction);
    }

    /**
     * @param BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doRefundMulti(BackendTransaction $transaction)
    {
        return $this->doRefund($transaction);
    }

    protected function getTransactionSums(Transaction $transaction)
    {
        $result     = [
            'blocked'    => 0,
            'sale'       => 0,
            'captured'   => 0,
            'refunded'   => 0,
            'authorized' => 0,
        ];
        $authorized = 0;

        $backendTransactions = $transaction->getBackendTransactions();

        if ($backendTransactions && count($backendTransactions) > 0) {
            foreach ($backendTransactions as $backendTransaction) {
                if (!$backendTransaction->isCompleted()) {
                    continue;
                }

                $value = $backendTransaction->getValue();
                switch ($backendTransaction->getType()) {
                    case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH:
                        $authorized += $value;

                        $result['blocked'] += $value;
                        break;

                    case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE:
                        $result['blocked'] += $value;
                        $result['sale']    += $value;
                        break;

                    case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE:
                    case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_PART:
                        $authorized -= $value;

                        $result['captured'] += $value;
                        $result['sale']     += $value;
                        break;

                    case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND:
                    case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART:
                        $result['refunded'] += $value;
                        $result['blocked']  -= $value;
                        $result['sale']     -= $value;
                        break;

                    case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI:
                        $authorized -= $value;

                        $result['refunded'] += $value;
                        $result['blocked']  -= $value;
                        $result['sale']     -= $value;
                        break;

                    case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID:
                    case \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID_PART:
                        $authorized -= $value;

                        $result['blocked'] -= $value;
                        break;

                    default:
                }

            }
        }

        $result['authorized'] += $authorized;

        $currency = $transaction->getCurrency();

        return array_map(static function ($item) use ($currency) {
            return $currency->roundValue($item);
        }, $result);
    }

    /**
     * @param Transaction $transaction
     * @param string      $captureId
     *
     * @return BackendTransaction|null
     */
    protected function getBackendTransactionByCaptureId(Transaction $transaction, $captureId)
    {
        foreach ($transaction->getBackendTransactions() as $backendTransaction) {
            $captureIdCell = $backendTransaction->getDataCell('PaypalCaptureID');
            if ($captureIdCell && $captureIdCell->getValue()) {
                return $backendTransaction;
            }
        }

        return null;
    }

    /**
     * @param Transaction $transaction
     * @param string      $authorizationId
     * @param string      $transactionType
     *
     * @return BackendTransaction|null
     */
    protected function getBackendTransactionByAuthorizationId(Transaction $transaction, $authorizationId, $transactionType)
    {
        foreach ($transaction->getBackendTransactions() as $backendTransaction) {
            $captureIdCell = $backendTransaction->getDataCell('PaypalAuthorizationID');
            if ($backendTransaction->getType() === $transactionType && $captureIdCell && $captureIdCell->getValue()) {
                return $backendTransaction;
            }
        }

        return null;
    }

    /**
     * @param Transaction $transaction
     * @param string      $refundId
     *
     * @return BackendTransaction|null
     */
    protected function getBackendTransactionByRefundId(Transaction $transaction, $refundId)
    {
        foreach ($transaction->getBackendTransactions() as $backendTransaction) {
            $captureIdCell = $backendTransaction->getDataCell('PaypalRefundID');
            if ($captureIdCell && $captureIdCell->getValue()) {
                return $backendTransaction;
            }
        }

        return null;
    }
}