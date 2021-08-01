<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\Model\Payment;

/**
 * Stripe payment processor
 */
class Stripe extends \XLite\Model\Payment\Base\Online
{
    const API_VERSION    = '2019-05-16';
    const APP_NAME       = 'X-Cart Stripe plugin';
    const APP_PARTNER_ID = 'pp_partner_DLMvmppc0YOIsZ';

    /**
     * Stripe library included flag
     *
     * @var boolean
     */
    protected $stripeLibIncluded = false;

    /**
     * Event id 
     * 
     * @var string
     */
    protected $eventId;

    /**
     * Get Webhook URL
     *
     * @return string
     */
    public function getWebhookURL()
    {
        return \XLite::getInstance()->getShopURL(
            \XLite\Core\Converter::buildURL('callback', null, array(), \XLite::getCustomerScript()),
            \XLite\Core\Config::getInstance()->Security->customer_security
        );
    }

    /**
     * Get URL of referral page
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getReferralPageURL(\XLite\Model\Payment\Method $method)
    {
        return '';
    }

    /**
     * Check - payment method connected to Stripe or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isSettingsConfigured(\XLite\Model\Payment\Method $method)
    {
        return ($method->getSetting('accessToken') && $method->getSetting('publishKey'))
            || ($method->getSetting('accessTokenTest') && $method->getSetting('publishKeyTest'));
    }

    /**
     * Check - payment method is configured or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        return $this->isSettingsConfigured($method)
            && \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * @return string
     */
    public function getActualClientSecret(\XLite\Model\Payment\Method $method)
    {
        $suffix = $this->isTestMode($method) ? 'Test' : '';
        return $method->getSetting('accessToken' . $suffix);
    }

    /**
     * Get allowed backend transactions
     *
     * @return array Status codes
     */
    public function getAllowedTransactions()
    {
        return array(
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE,            
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_PART,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI,
        );
    }

    /**
     * Get settings widget or template
     *
     * @return string Widget class name or template path
     */
    public function getSettingsWidget()
    {
        return '\XLite\Module\XC\Stripe\View\Config';
    }

    /**
     * Get input template
     *
     * @return string
     */
    public function getInputTemplate()
    {
        return 'modules/XC/Stripe/payment.twig';
    }

    /**
     * Return true if payment method settings form should use default submit button.
     * Otherwise, settings widget must define its own button
     *
     * @return boolean
     */
    public function useDefaultSettingsFormButton()
    {
        return false;
    }

    /**
     * Get initial transaction type (used when customer places order)
     *
     * @param \XLite\Model\Payment\Method $method Payment method object OPTIONAL
     *
     * @return string
     */
    public function getInitialTransactionType($method = null)
    {
        $type = $method ? $method->getSetting('type') : $this->getSetting('type');

        return 'sale' == $type
            ? \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE
            : \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH;
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
     * Do initial payment
     *
     * @return string Status code
     */
    protected function doInitialPayment()
    {
        $result = static::COMPLETED;
        $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS;

        $type = $this->getInitialTransactionType();
        $backendTransaction = $this->registerBackendTransaction($type);
        $backendTransaction->setDataCell('stripe_id', $this->request['id']);
        $this->transaction->setType($type);
        $this->setDetail('stripe_id', $this->request['id']);
        
        if ($this->request['error']) {
            $result = static::FAILED;
            $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_FAILED;
            $backendTransaction->setDataCell('error', $this->request['error']);
            $this->transaction->setNote($this->request['error']);
            $this->setDetail('Error', $this->request['error']);

            static::log('Error: ' . __FUNCTION__ . $this->request['error']);
        } else {
            static::log('Success: ' . __FUNCTION__);
        }

        $backendTransaction->setStatus($backendTransactionStatus);
        $backendTransaction->registerTransactionInOrderHistory('initial request');

        return $result;
    }

    /**
     * Confirm payment
     *
     * @param \XLite\Model\Cart $cart
     *
     * @return string Status code
     */
    public function confirmPayment($cart)
    {
        $this->includeStripeLibrary();

        $this->transaction = $cart->getFirstOpenPaymentTransaction();
        $this->request = \XLite\Core\Request::getInstance();

        try {
            if ($this->request->payment_method_id) {
                $intent = \Stripe\PaymentIntent::create(
                    [
                        'amount'              => $this->formatCurrency($this->transaction->getValue()),
                        'currency'            => $this->transaction->getCurrency()->getCode(),
                        'confirmation_method' => 'manual',
                        'capture_method'      => $this->isCapture() ? 'automatic' : 'manual',
                        'confirm'             => true,
                        'payment_method_data' => [
                            'type' => 'card',
                            'card' => ['token' => $this->request->payment_method_id],
                        ],
                        'description'         => static::t('Payment transaction ID') . ': ' . $this->transaction->getPublicId(),
                        'metadata'            => ['txnId' => $this->transaction->getPublicTxnId()],
                    ],
                    [
                        'idempotency_key' => uniqid('', true),
                    ]
                );
            }
            if ($this->request->payment_intent_id) {
                $intent = \Stripe\PaymentIntent::retrieve(
                    $this->request->payment_intent_id
                );
                $intent->confirm(
                    null,
                    [
                        'idempotency_key' => uniqid('', true),
                    ]
                );
            }

            $stripeResponce = $this->getPaymentResponse($intent);

            static::log([
                'message'   => 'Success: ' . __FUNCTION__,
                'request'   => $this->request->getPostDataWithArrayValues(),
                'response'  => $stripeResponce
            ]);

            echo json_encode($stripeResponce);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            static::log([
                'message'          => 'Error: ' . __FUNCTION__,
                'request'          => $this->request->getPostDataWithArrayValues(),
                'exceptionMessage' => $e->getMessage()
            ]);

            echo json_encode([
                'error' => $e->getMessage(),
                'error_code' => $e->getStripeCode(),
                'human_error' => static::t('StripeJSerror ' . $e->getStripeCode()),
                'stripe_id' => $e->getJsonBody()['error']['payment_intent']['id'] ?? 0
            ]);
        }

        die;
    }

    /**
     * Get payment response
     *
     * @param \Stripe\PaymentIntent $intent
     *
     * @return array response
     */
    protected function getPaymentResponse($intent) {
        if ($intent->status == 'requires_action' &&
            $intent->next_action->type == 'use_stripe_sdk') {

            $responce = [
                'requires_action' => true,
                'payment_intent_client_secret' => $intent->client_secret
            ];
        } else if (in_array($intent->status, ['succeeded', 'requires_capture'])) {
            $responce = [
                "success" => true
            ];

            if (!$this->checkTotal($this->transaction->getCurrency()->convertIntegerToFloat($intent->amount))) {
                $responce['error'] = "Total amount doesn't match.";
            } elseif (!$this->checkCurrency(strtoupper($intent->currency))) {
                $responce['error'] = "Currency code doesn't match.";
            }
        } else {
            $responce = [
                'error' => 'Invalid PaymentIntent status'
            ];
        }

        $responce['stripe_id'] = $intent->id;

        return $responce;
    }

    /**
     * Format currency 
     * 
     * @param float $value Currency value
     *  
     * @return integer
     */
    protected function formatCurrency($value)
    {
        return $this->transaction->getCurrency()->roundValueAsInteger($value);
    }

    /**
     * Check - transaction is capture type or not
     * 
     * @return boolean
     */
    protected function isCapture()
    {
        return $this->getInitialTransactionType() === \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE;
    }

    /**
     * Register backend transaction 
     * 
     * @param string                           $type        Backend transaction type OPTIONAL
     * @param \XLite\Model\Payment\Transaction $transaction Transaction OPTIONAL
     *  
     * @return \XLite\Model\Payment\BackendTransaction
     */
    protected function registerBackendTransaction($type = null, \XLite\Model\Payment\Transaction $transaction = null)
    {
        if (!$transaction) {
            $transaction = $this->transaction;
        }

        if (!$type) {
            $type = $transaction->getType();
        }

        $backendTransaction = $transaction->createBackendTransaction($type);

        return $backendTransaction;
    }

    /**
     * Include Stripe library
     *
     * @return void
     */
    protected function includeStripeLibrary()
    {
        if (!$this->stripeLibIncluded) {
            require_once LC_DIR_MODULES . 'XC' . LC_DS . 'Stripe' . LC_DS . 'lib' . LC_DS . 'vendor' . LC_DS . 'autoload.php';

            if ($this->transaction) {
                $method = $this->transaction->getPaymentMethod();
                $key = $this->getActualClientSecret($method);

            } else {
                $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
                    ->findOneBy(array('service_name' => 'Stripe'));
                $key = $this->getActualClientSecret($method);
            }

            \Stripe\Stripe::setApiKey($key);
            \Stripe\Stripe::setApiVersion(static::API_VERSION);

            $module = \Includes\Utils\Module\Manager::getRegistry()->getModule('XC', 'Stripe');
            \Stripe\Stripe::setAppInfo(
                static::APP_NAME,
                $module->getVersion(),
                'https://market.x-cart.com/addons/stripe-payment-module.html',
                static::APP_PARTNER_ID
            );

            $this->stripeLibIncluded = true;
        }
    }

    // {{{ Secondary transactions

    /**
     * Capture
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doCapture(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $this->includeStripeLibrary();

        $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_FAILED;

        try {
            /** @var \Stripe\PaymentIntent $paymentIntent */
            $paymentIntent = \Stripe\PaymentIntent::retrieve(
                $transaction->getPaymentTransaction()->getDataCell('stripe_id')->getValue()
            );
            $paymentIntent->capture();

            if ($paymentIntent->status == 'succeeded') {
                $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS;

                static::log([
                    'message' => 'Success: ' . __FUNCTION__,
                    'id'      => $paymentIntent->id,
                    'amount'  => $paymentIntent->amount,
                    'status'  => $paymentIntent->status
                ]);
            }

            if (!empty($paymentIntent->charges->data)) {
                $charge = reset($paymentIntent->charges->data);
                $transaction->setDataCell('stripe_b_txntid', $charge->balance_transaction);
            }

        } catch (\Exception $e) {
            $transaction->setDataCell('errorMessage', $e->getMessage());
            static::log(__FUNCTION__ . ' failed: ' . $e->getMessage());
            \XLite\Core\TopMessage::addError($e->getMessage());
        }

        $transaction->setStatus($backendTransactionStatus);
         
        return \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS == $backendTransactionStatus;
    }

    /**
     * Void
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doVoid(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $this->includeStripeLibrary();

        $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_FAILED;

        try {
            /** @var \Stripe\PaymentIntent $paymentIntent */
            $paymentIntent = \Stripe\PaymentIntent::retrieve(
                $transaction->getPaymentTransaction()->getDataCell('stripe_id')->getValue()
            );
            $paymentIntent->cancel();

            if ($paymentIntent->status == 'canceled') {
                $charge = reset($paymentIntent->charges->data);
                if ($charge && $charge->refunds->data) {
                    $refundTransaction = reset($charge->refunds->data);

                    if ($refundTransaction) {
                        $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS;

                        $transaction->setDataCell('stripe_date', $refundTransaction->created);
                        if ($refundTransaction->balance_transaction) {
                            $transaction->setDataCell('stripe_b_txntid', $refundTransaction->balance_transaction);
                        }
                    }
                }

                $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS;

                static::log([
                    'message' => 'Success: ' . __FUNCTION__,
                    'id'      => $paymentIntent->id,
                    'amount'  => $paymentIntent->amount,
                    'status'  => $paymentIntent->status
                ]);
            }

        } catch (\Exception $e) {
            $transaction->setDataCell('errorMessage', $e->getMessage());
            static::log(__FUNCTION__ . ' failed: ' . $e->getMessage());
            \XLite\Core\TopMessage::addError($e->getMessage());
        }

        $paymentTransaction = $transaction->getPaymentTransaction();

        $transaction->setStatus($backendTransactionStatus);
        $paymentTransaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_VOID);

        return \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS == $backendTransactionStatus;
    }


    /**
     * Refund
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doRefundMulti(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        return $this->doRefund($transaction);
    }

    /**
     * Refund
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doRefund(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $this->includeStripeLibrary();

        $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_FAILED;

        try {
            /** @var \Stripe\PaymentIntent $paymentIntent */
            $paymentIntent = \Stripe\PaymentIntent::retrieve(
                $transaction->getPaymentTransaction()->getDataCell('stripe_id')->getValue()
            );

            $payment = !empty($paymentIntent->charges->data)
                ? reset($paymentIntent->charges->data)
                : null;

            if (!$payment) {
                throw new \Exception('No charges found for payment intent ' . $paymentIntent->id);
            }

            if ($transaction->getValue() != $transaction->getPaymentTransaction()->getValue()) {

                $payment->refunds->create([
                    'amount' => $this->formatCurrency($transaction->getValue()),
                ]);

                /** @var \Stripe\Refund $refundTransaction */
                $refundTransaction = null;

                if ($payment->refunds) {
                    foreach ($payment->refunds->all() as $r) {
                        if (!$this->isRefundTransactionRegistered($r)) {
                            $refundTransaction = $r;
                            break;
                        }
                    }
                }

            } else {
                $refundTransaction = $payment->refunds->create();
            }

            if ($refundTransaction) {
                $backendTransactionStatus = \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS;

                $transaction->setDataCell('stripe_date', $refundTransaction->created);
                if ($refundTransaction->balance_transaction) {
                    $transaction->setDataCell('stripe_b_txntid', $refundTransaction->balance_transaction);
                }

                static::log([
                    'message'             => 'Success: ' . __FUNCTION__,
                    'id'                  => $refundTransaction->id,
                    'amount'              => $refundTransaction->amount,
                    'balance_transaction' => $refundTransaction->balance_transaction
                ]);
            }

        } catch (\Exception $e) {
            $transaction->setDataCell('errorMessage', $e->getMessage());
            static::log(__FUNCTION__ . ' failed: ' . $e->getMessage());
            \XLite\Core\TopMessage::addError($e->getMessage());
        }

        $transaction->setStatus($backendTransactionStatus);

        return \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS == $backendTransactionStatus;
    }

    /**
     * Check - specified rfund transaction is registered or not
     * 
     * @param object $refund Refund transaction
     *  
     * @return boolean
     */
    protected function isRefundTransactionRegistered($refund)
    {
        $result = null;
        $types = array(
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI,
        );

        foreach ($this->transaction->getBackendTransactions() as $bt) {
            $txnid = $bt->getDataCell('stripe_b_txntid');
            if (
                in_array($bt->getType(), $types)
                && (!$txnid || $txnid->getValue() == $refund->balance_transaction)
                && ($bt->getDataCell('stripe_date') && $bt->getDataCell('stripe_date')->getValue() == $refund->created)
            ) {
                $result = $bt;
                break;
            }
        }

        return $result;
    }

    protected function getRefundObject($event)
    {
        $refunds = $event->data->object->refunds instanceof \Stripe\Collection
            ? $event->data->object->refunds->data
            : $event->data->object->refunds;

        foreach ($refunds as $r) {
            if (!$this->isRefundTransactionRegistered($r)) {
                return $r;
            }
        }

        return null;
    }

    // }}}

    // {{{ Callback

    /**
     * Get callback owner transaction
     * 
     * @return \XLite\Model\Payment\Transaction
     */
    public function getCallbackOwnerTransaction()
    {
        $transaction = null;

        $eventId = $this->detectEventId();
        if ($eventId) {
            $this->includeStripeLibrary();

            try {
                $event = \Stripe\Event::retrieve($eventId);
                if ($event) {
                    if (isset($event->data->object->metadata->txnId)) {
                        $transaction = \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')
                            ->findOneBy(['publicTxnId' => $event->data->object->metadata->txnId]);
                    } else {
                        $pi_id = $event->data->object->object === 'charge'
                            ? $event->data->object->payment_intent
                            : $event->data->object->id;
                        $transaction = \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')
                            ->findOneByCell('stripe_id', $pi_id);
                    }

                    if ($transaction) {
                        $this->eventId = $eventId;
                    }
                }

            } catch (\Exception $e) {
            }
        }

        return $transaction;
    }

    /**
     * Process callback
     *
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     *
     * @throws \XLite\Core\Exception\PaymentProcessing\ACallbackException
     */
    public function processCallback(\XLite\Model\Payment\Transaction $transaction)
    {
        parent::processCallback($transaction);

        if ($this->canProcessCallback($transaction)) {
            $this->processStripeEvent($transaction);

            // Remove ttl for IPN requests
            if ($transaction->isEntityLocked(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN)) {
                $transaction->unsetEntityLock(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN);
            }

        } else {
            throw new \XLite\Core\Exception\PaymentProcessing\CallbackNotReady();
        }
    }

    /**
     * @inheritdoc
     */
    public function processCallbackNotReady(\XLite\Model\Payment\Transaction $transaction)
    {
        parent::processCallbackNotReady($transaction);

        header('HTTP/1.1 409 Conflict', true, 409);
        header('Status: 409 Conflict');
        header('X-Robots-Tag: noindex, nofollow');
    }

    /**
     * Check if we can process IPN right now or should receive it later
     *
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     *
     * @return boolean
     */
    protected function canProcessCallback(\XLite\Model\Payment\Transaction $transaction)
    {
        $locked = $transaction->isEntityLocked(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN);
        $result = $transaction->isEntityLockExpired(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN)
            || !$locked;

        // Set ttl once when no payment return happened yet
        if (!$locked && !$this->isOrderProcessed($transaction)) {
            $transaction->setEntityLock(\XLite\Model\Payment\Transaction::LOCK_TYPE_IPN);
            $result = false;
        }

        return $result;
    }

    /**
     * Checks if the order of transaction is already processed and is available for IPN receiving
     *
     * @param \XLite\Model\Payment\Transaction $transaction
     * @return bool
     */
    protected function isOrderProcessed(\XLite\Model\Payment\Transaction $transaction)
    {
        return !$transaction->isOpen() && !$transaction->isInProgress() && $transaction->getOrder()->getOrderNumber();
    }

    /**
     * Process generic stripe event
     *
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     */
    protected function processStripeEvent($transaction)
    {
        $this->includeStripeLibrary();

        try {
            $event = \Stripe\Event::retrieve($this->eventId);
            if ($event) {
                $name = 'processEvent' . \XLite\Core\Converter::convertToCamelCase(str_replace('.', '_', $event->type));
                if (method_exists($this, $name)) {
                    // $name assembled from 'processEvent' + event type
                    $this->$name($event, $transaction);
                    \XLite\Core\Database::getEM()->flush();
                }

                static::log('Event handled: ' . $event->type . ' # ' . $this->eventId . PHP_EOL
                    . 'Processed: ' . (method_exists($this, $name) ? 'Yes' : 'No'));
            }

        } catch (\Exception $e) {
        }
    }

    /**
     * Process event charge.refunded
     *
     * @param \Stripe_Event $event Event
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     *
     * @return void
     */
    protected function processEventChargeRefunded($event, $transaction)
    {
        $refundTransaction = $this->getRefundObject($event);

        if ($refundTransaction
            && !$this->isBackendTransactionSuccessful(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND)) {
            $amount = $this->transaction->getCurrency()->convertIntegerToFloat($refundTransaction->amount);

            if ($amount != $this->transaction->getValue()) {
                $backendTransaction = $this->registerBackendTransaction(
                    \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART
                );
                $backendTransaction->setValue($amount);

            } else {
                $type = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND;
                if (!$this->transaction->isCaptured()) {
                    $type = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID;
                    $this->transaction->setType($type);
                    $this->transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_VOID);
                }
                $backendTransaction = $this->registerBackendTransaction($type);
            }

            $backendTransaction->setDataCell('stripe_date', $refundTransaction->created);
            if ($refundTransaction->balance_transaction) {
                $backendTransaction->setDataCell('stripe_b_txntid', $refundTransaction->balance_transaction);
            }

            $backendTransaction->setStatus(\XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS);
            $backendTransaction->registerTransactionInOrderHistory('callback');

        } elseif ($this->isBackendTransactionSuccessful(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND)) {
            $this->transaction->setType(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND);
            $this->transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);
        } else{
            static::log('Duplicate charge.refunded event # ' . $event->id);
        }
    }

    /**
     * Process event charge.captured 
     * 
     * @param \Stripe_Event $event Event
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     *  
     * @return void
     */
    protected function processEventChargeCaptured($event, $transaction)
    {
        $refundTransaction = $this->getRefundObject($event);

        if (!$this->isBackendTransactionSuccessful(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE)) {
            $amount = $this->transaction->getValue();
            if ($refundTransaction) {
                $amountRefunded = $this->transaction->getCurrency()->convertIntegerToFloat($refundTransaction->amount);
                $amountFull = $this->transaction->getCurrency()->convertIntegerToFloat($event->data->object->amount);
                $amount = $amountFull - $amountRefunded;
                if ($amount != $this->transaction->getValue()) {
                    $backendTransaction = $this->registerBackendTransaction(
                        \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI
                    );
                    $backendTransaction->setValue($amountRefunded);
                    $backendTransaction->setStatus(\XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS);
                    $backendTransaction->registerTransactionInOrderHistory('callback');
                }                
            }

            $type = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE;
            if ($refundTransaction) {
                $type = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_PART;
                $backendTransaction = $this->registerBackendTransaction($type);
                $backendTransaction->setValue($amount);

                $this->transaction->setType(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_PART);                    
                $this->transaction->setValue($amount);
                $this->transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);

            }else{                
                $backendTransaction = $this->registerBackendTransaction($type);
            }
            $backendTransaction->setStatus(\XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS);
            $backendTransaction->registerTransactionInOrderHistory('callback');

        } else {
            static::log('Duplicate charge.captured event # ' . $event->id);
        }
    }

    /**
     * Process event charge.captured
     *
     * @param \Stripe_Event $event Event
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     *
     * @return void
     */
    protected function processEventChargeSucceeded($event, $transaction)
    {
        $type = $event->data->object->captured
            ? \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE
            : \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH;

        $backendTransaction = $this->transaction->getInitialBackendTransaction();

        if (!$backendTransaction) {
            $backendTransaction = $this->registerBackendTransaction($type);
        }

        if ($backendTransaction->getDataCell('event_id') !== $event->id) {
            $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);
            $backendTransaction->setStatus(\XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS);
            $backendTransaction->setDataCell('stripe_id', $event->data->object->payment_intent);
            $backendTransaction->setDataCell('event_id', $event->id);

            if (!empty($event->data->object->balance_transaction)) {
                $backendTransaction->setDataCell('stripe_b_txntid', $event->data->object->balance_transaction);
            }

            $backendTransaction->registerTransactionInOrderHistory('callback');
        } else {
            static::log('Duplicate charge.succeeded event # ' . $event->id);
        }
    }

    /**
     * Process event charge.captured
     *
     * @param \Stripe_Event $event Event
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     *
     * @return void
     */
    protected function processEventChargePending($event, $transaction)
    {
        $pending = \XLite\Model\Payment\Transaction::STATUS_PENDING;

        $type = $event->data->object->captured
            ? \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE
            : \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH;

        $backendTransaction = $this->transaction->getInitialBackendTransaction();

        if (!$backendTransaction) {
            $backendTransaction = $this->registerBackendTransaction($type);
        }

        if ($backendTransaction->getDataCell('event_id') !== $event->id && $this->transaction->getStatus() !== $pending) {

            $backendTransaction = $this->registerBackendTransaction($type);
            $backendTransaction->setStatus($pending);
            $backendTransaction->setDataCell('stripe_id', $event->data->object->payment_intent);
            $backendTransaction->setDataCell('event_id', $event->id);

            if (!empty($event->data->object->balance_transaction)) {
                $backendTransaction->setDataCell('stripe_b_txntid', $event->data->object->balance_transaction);
            }

            $backendTransaction->registerTransactionInOrderHistory('callback');

            $this->transaction->setStatus($pending);
        } else {
            static::log('Duplicate charge.pending event # ' . $event->id);
        }
    }

    /**
     * Process event charge.captured
     *
     * @param \Stripe_Event $event Event
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     *
     * @return void
     */
    protected function processEventChargeFailed($event, $transaction)
    {
        $failed = \XLite\Model\Payment\Transaction::STATUS_FAILED;

        if ($this->transaction->getStatus() !== $failed) {
            $this->setDetail(
                'status',
                $event->data->object->failure_message,
                'Status'
            );

            $this->transaction->setStatus($failed);
            $backendTransaction = $this->transaction->getInitialBackendTransaction();

            if (null !== $backendTransaction && $backendTransaction->getDataCell('event_id') !== $event->id) {
                $backendTransaction->setStatus($failed);
                $backendTransaction->setDataCell('stripe_id', $event->data->object->payment_intent);
                $backendTransaction->setDataCell('event_id', $event->id);

                if (!empty($event->data->object->balance_transaction)) {
                    $backendTransaction->setDataCell('stripe_b_txntid', $event->data->object->balance_transaction);
                }

                $backendTransaction->registerTransactionInOrderHistory('callback');
            } else {
                static::log('Duplicate charge.failed event # ' . $event->id);
            }
        }
    }

    /**
     * Check if event is already handled
     *
     * @param string $type
     *
     * @return bool
     */
    protected function isBackendTransactionSuccessful($type)
    {
        foreach ($this->transaction->getBackendTransactions() as $bt) {
            if (
                $bt->getType() == $type
                && $bt->getStatus() == \XLite\Model\Payment\BackendTransaction::STATUS_SUCCESS
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if event is present in the database
     *
     * @param string $id
     *
     * @return \XLite\Model\Payment\BackendTransaction
     */
    protected function getBackendTransactionByChargeId($id)
    {
        $ref = \XLite\Core\Database::getRepo('XLite\Model\Payment\BackendTransactionData')
            ->findOneBy(
                array(
                    'name' => 'stripe_id',
                    'value' => $id,
                )
            );

        return $ref ? $ref->getTransaction() : null;
    }

    /**
     * Detect event id 
     * 
     * @return string
     */
    protected function detectEventId()
    {
        $body = @file_get_contents('php://input');
        $event = @json_decode($body);
        $id = $event ? $event->id : null;

        return ($id && preg_match('/^evt_/Ss', $id)) ? $id : null;
    }

    /**
     * Logging the data under Stripe
     * Available if developer_mode is on in the config file
     *
     * @param mixed $data Log data
     *
     * @return void
     */
    protected static function log($data)
    {
        if (LC_DEVELOPER_MODE) {
            \XLite\Logger::logCustom('Stripe', $data);
        }
    }

    // }}}

    // {{{ Service requests

    /**
     * Retrieve acount 
     * 
     * @return \Stripe\Account
     */
    public function retrieveAcount()
    {
        $this->includeStripeLibrary();

        try {
            $account = \Stripe\Account::retrieve();

        } catch (\Exception $e) {
            $account = null;
        }

        return $account;
    }

    // }}}

}

