<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Model\Payment\Processor;

use XLite\Core\Database;
use XLite\Core\TopMessage;
use XLite\Model\Payment\BackendTransaction;
use XLite\Model\Payment\Transaction;
use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Plan;
use XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription;
use XPaymentsCloud\Model\Subscription as XpaymentsSubscription;
use XPaymentsCloud\ApiException;
use XPaymentsCloud\Model\Payment as XpPayment;
use XLite\Core\Session;
use XLite\Module\XPay\XPaymentsCloud\Model\Payment\XpaymentsFraudCheckData as FraudCheckData;
use \XLite\Model\Order;
use \XLite\Module\XPay\XPaymentsCloud\Main as ModuleMain;

class XPaymentsCloud extends \XLite\Model\Payment\Base\CreditCard
{
    /***
     * Simply redirect to 3-D Secure page instead of embedding it
     */
    const SIMPLE_3D_SECURE_MODE = false;

    /**
     * Allowed secondary actions status values for transactions
     */
    const ACTION_ALLOWED = 'Yes';
    const ACTION_PART = 'Yes, partial';
    const ACTION_MULTI = 'Yes, multiple';
    const ACTION_NOTALLOWED = 'No';

    /**
     * List of transaction types used for managing fraud
     */
    protected $fraudTransactionTypes = [
        BackendTransaction::TRAN_TYPE_ACCEPT,
        BackendTransaction::TRAN_TYPE_DECLINE,
    ];

    /**
     * Get allowed backend transactions
     *
     * @return string[] Status code
     */
    public function getAllowedTransactions()
    {
        return array(
            BackendTransaction::TRAN_TYPE_CAPTURE,
            BackendTransaction::TRAN_TYPE_CAPTURE_PART,
            BackendTransaction::TRAN_TYPE_VOID,
            BackendTransaction::TRAN_TYPE_REFUND,
            BackendTransaction::TRAN_TYPE_REFUND_PART,
            BackendTransaction::TRAN_TYPE_REFUND_MULTI,
            BackendTransaction::TRAN_TYPE_ACCEPT,
            BackendTransaction::TRAN_TYPE_DECLINE,
        );
    }

    /**
     * Do initial payment
     *
     * @return string Status code
     */
    protected function doInitialPayment()
    {
        $api = $this->initClient();

        $token = \XLite\Core\Request::getInstance()->xpaymentsToken;

        $cartHasSubscriptions = $this->transaction
            ->getOrder()
            ->hasXpaymentsSubscriptionItems();

        if ($cartHasSubscriptions) {
            $this->assignOrderItemsUniqueIds();
        }

        try {
            $response = $api->doPay(
                $token,
                $this->getTransactionId(),
                $this->getXpaymentsCustomerId(),
                $this->prepareCart(),
                $this->getReturnURL(null, true),
                $this->getCallbackURL(null, true),
                $cartHasSubscriptions ? true : null
            );

            $payment = $response->getPayment();
            $status = $payment->status;
            $note = $response->message ?: $payment->message;

            if (!is_null($response->redirectUrl)) {
                // Should redirect to continue payment
                $this->transaction->setXpaymentsId($payment->xpid);

                $url = $response->redirectUrl;
                if (!\XLite\Core\Converter::isURL($url)) {
                    throw new \XPaymentsCloud\ApiException('Invalid 3-D Secure URL');
                }

                if (static::SIMPLE_3D_SECURE_MODE) {
                    $result = static::PROLONGATION;
                    $this->redirectToPay($url);
                } else {
                    $result = static::SEPARATE;

                    Session::getInstance()->xpaymentsData = [
                        'redirectUrl' => $url,
                    ];
                }

            } else {
                $result = $this->processPaymentFinish($this->transaction, $payment);

                $xpaymentsSubscriptions = $response->getSubscriptions();
                if ($xpaymentsSubscriptions) {
                    $this->createSubscriptions($xpaymentsSubscriptions);
                }

                if (static::FAILED == $result) {
                    TopMessage::addError($note);
                }

            }

        } catch (\XPaymentsCloud\ApiException $exception) {
            $result = static::FAILED;
            $note = $exception->getMessage();
            $this->transaction->setDataCell('xpaymentsMessage', $note, 'Message');

            $this->handleApiException($exception, 'Failed to process the payment!');
        }

        $this->transaction->setNote(substr($note, 0, 255));

        return $result;
    }

    /**
     * @param XpaymentsSubscription[] $xpaymentsSubscriptions
     *
     * @return void
     * @throws \Doctrine\ORM\ORMException
     */
    protected function createSubscriptions(array $xpaymentsSubscriptions)
    {
        $order = $this->transaction->getOrder();
        $shippingAddress = $order->getProfile()->getShippingAddress();
        $shippingId = $order->getShippingId();

        foreach ($xpaymentsSubscriptions as $key => $xpaymentsSubscription) {
            $item = null;
            if ($xpaymentsSubscription->getUniqueOrderItemId()) {
                /** @var \XLite\Model\OrderItem $item */
                $item = Database::getRepo('XLite\Model\OrderItem')
                    ->findOneBy([
                        'xpaymentsUniqueId' => $xpaymentsSubscription->getUniqueOrderItemId(),
                    ]);
            }

            if ($item) {
                $subscription = new Subscription();
                $item->setXpaymentsSubscription($subscription);
                $subscription->setInitialOrderItem($item)
                    ->setShippingId($shippingId)
                    ->setShippingAddress($shippingAddress)
                    ->setCalculateShipping($item->getProduct()->getXpaymentsSubscriptionPlan()->getCalculateShipping())
                    ->createOrUpdateFromXpaymentsSubscription($xpaymentsSubscription);
                Database::getEM()->persist($subscription);
            }
        }
        Database::getEM()->flush();
    }

    /**
     * Assign unique ids for mapping of X-Payments subscriptions with X-Cart order items
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function assignOrderItemsUniqueIds()
    {
        $items = $this->transaction->getOrder()->getItems();

        foreach ($items as $item) {
            if ($item->isXpaymentsSubscription()) {
                $item->setXpaymentsUniqueId(\XLite\Core\Converter::generateRandomToken());
            }
        }

        Database::getEM()->flush();
    }

    /**
     * @param \XLite\Model\Payment\Transaction $transaction
     *
     * @return boolean
     */
    protected function isCaptureTransactionAllowed(Transaction $transaction)
    {
        return $transaction->isCaptureTransactionAllowed()
            && (static::ACTION_NOTALLOWED != $transaction->getDetail('xpaymentsCapture'))
            && !$this->isManualReviewFraudStatus($transaction)
            && !$this->isPendingFraudStatus($transaction);
    }

    /**
     * @param \XLite\Model\Payment\Transaction $transaction
     *
     * @return boolean
     */
    protected function isRefundTransactionAllowed(Transaction $transaction)
    {
        return $transaction->isRefundTransactionAllowed()
            && (static::ACTION_NOTALLOWED != $transaction->getDetail('xpaymentsRefund'))
            && !$this->isManualReviewFraudStatus($transaction)
            && !$this->isPendingFraudStatus($transaction);
    }

    /**
     * @param \XLite\Model\Payment\Transaction $transaction
     *
     * @return boolean
     */
    protected function isVoidTransactionAllowed(Transaction $transaction)
    {
        return $transaction->isVoidTransactionAllowed()
            && (static::ACTION_NOTALLOWED != $transaction->getDetail('xpaymentsVoid'))
            && !$this->isManualReviewFraudStatus($transaction)
            && !$this->isPendingFraudStatus($transaction);
    }

    /**
     * Check capture (partially) operation availability
     *
     * @param \XLite\Model\Payment\Transaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function isCapturePartTransactionAllowed(\XLite\Model\Payment\Transaction $transaction)
    {
        return $transaction->isCaptureTransactionAllowed()
            && in_array(
                $transaction->getDetail('xpaymentsCapture'),
                [
                    static::ACTION_PART,
                    static::ACTION_MULTI
                ]
            )
            && !$this->isManualReviewFraudStatus($transaction)
            && !$this->isPendingFraudStatus($transaction);
    }

    /**
     * Check capture (multiple) operation availability
     *
     * @param \XLite\Model\Payment\Transaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function isCaptureMultiTransactionAllowed(\XLite\Model\Payment\Transaction $transaction)
    {
        return
            false && // Currently not supported
            $transaction->isCaptureTransactionAllowed()
            && (static::ACTION_MULTI == $transaction->getDetail('xpaymentsCapture'))
            && !$this->isManualReviewFraudStatus($transaction)
            && !$this->isPendingFraudStatus($transaction);
    }

    /**
     * Check refund (partially) operation availability
     *
     * @param \XLite\Model\Payment\Transaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function isRefundPartTransactionAllowed(\XLite\Model\Payment\Transaction $transaction)
    {
        return $transaction->isRefundPartTransactionAllowed()
            && in_array(
                $transaction->getDetail('xpaymentsRefund'),
                [
                    static::ACTION_PART,
                    static::ACTION_MULTI
                ]
            )
            && !$this->isManualReviewFraudStatus($transaction)
            && !$this->isPendingFraudStatus($transaction);
    }

    /**
     * Check refund (multiple) operation availability
     *
     * @param \XLite\Model\Payment\Transaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function isRefundMultiTransactionAllowed(\XLite\Model\Payment\Transaction $transaction)
    {
        return $transaction->isRefundMultiTransactionAllowed()
            && (static::ACTION_MULTI == $transaction->getDetail('xpaymentsRefund'))
            && !$this->isManualReviewFraudStatus($transaction)
            && !$this->isPendingFraudStatus($transaction);
    }

    /**
     * @param Transaction $transaction
     *
     * @return bool
     */
    protected function isPendingFraudStatus(Transaction $transaction)
    {
        $result = false;
        $fraudData = $transaction->getXpaymentsFraudCheckData();
        if ($fraudData) {
            foreach ($fraudData as $fraudDataItem) {
                if ($fraudDataItem->isPending()) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @param Transaction $transaction
     *
     * @return bool
     */
    protected function isManualReviewFraudStatus(Transaction $transaction)
    {
        $result = false;
        $fraudData = $transaction->getXpaymentsFraudCheckData();
        if ($fraudData) {
            foreach ($fraudData as $fraudDataItem) {
                if ($fraudDataItem->isManualReview()) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @param Transaction $transaction
     *
     * @return bool
     */
    protected function isAcceptTransactionAllowed(Transaction $transaction)
    {
        return $this->isManualReviewFraudStatus($transaction);
    }

    /**
     * @param Transaction $transaction
     *
     * @return bool
     */
    protected function isDeclineTransactionAllowed(Transaction $transaction)
    {
        return $this->isManualReviewFraudStatus($transaction);
    }

    /**
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doCapture(BackendTransaction $transaction)
    {
        return $this->doSecondary('capture', $transaction);
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
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doRefund(BackendTransaction $transaction)
    {
        return $this->doSecondary('refund', $transaction);
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

    /**
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doVoid(BackendTransaction $transaction)
    {
        $result = $this->doSecondary('void', $transaction);
        
        if ($result) {
            $transaction->getPaymentTransaction()->setStatus(Transaction::STATUS_VOID);
        }

        return $result;
    }

    /**
     * @param BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doAccept(BackendTransaction $transaction)
    {
        return $this->doSecondary('accept', $transaction);
    }

    /**
     * @param BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doDecline(BackendTransaction $transaction)
    {
        return $this->doSecondary('decline', $transaction);
    }

    /**
     * @param Order $order
     * @param Transaction $parentCardTransaction
     * @param float $amount
     * @param string $xpid
     * @return string
     * @throws \Doctrine\ORM\ORMException
     */
    public function doRebill(Order $order, float $amount, string $xpid)
    {
        $newTransaction = new Transaction();
        $newTransaction->setPaymentMethod(\XLite\Module\XPay\XPaymentsCloud\Main::getPaymentMethod());
        $newTransaction->setStatus(Transaction::STATUS_INPROGRESS);
        $newTransaction->setValue($amount);
        $newTransaction->renewTransactionId();

        $order->addPaymentTransactions($newTransaction);
        $newTransaction->setOrder($order);

        $this->transaction = $newTransaction;

        Database::getEM()->persist($newTransaction);
        Database::getEM()->flush();

        $api = $this->initClient();

        $cart = $this->prepareRebillCart($amount, $order->getOrderNumber());

        try {
            $response = $api->doRebill(
                $this->getTransactionId(),
                $this->getXpaymentsCustomerId(),
                $this->getCallbackURL(null, true),
                $xpid,
                $cart
            );

            $payment = $response->getPayment();
            $note = $response->message ?: $payment->message;

            $result = $this->processPaymentFinish($newTransaction, $payment);
            if (static::FAILED == $result) {
                TopMessage::addError($note);
            } else {
                $newTransaction->setStatus(Transaction::STATUS_SUCCESS);
                TopMessage::addInfo($note);
            }

        } catch (\XPaymentsCloud\ApiException $exception) {
            $result = static::FAILED;
            $note = $exception->getMessage();
            $this->transaction->setDataCell('xpaymentsMessage', $note, 'Message');

            $this->handleApiException($exception, 'Failed to process the rebill!');
        }

        $this->transaction->setNote(substr($note, 0, 255));

        Database::getEM()->flush();

        return $result;
    }

    /**
     * Auxiliary function for secondary actions execution
     *
     * @param $action
     * @param BackendTransaction $transaction
     *
     * @return bool
     */
    protected function doSecondary($action, BackendTransaction $transaction)
    {
        $paymentTransaction = $transaction->getPaymentTransaction();

        $api = $this->initClient();

        try {
            $methodName = 'do' . ucfirst($action);
            /** @var \XPaymentsCloud\Response $response */
            $response = $api->$methodName(
                $paymentTransaction->getXpaymentsId(),
                $transaction->getValue()
            );

            $payment = $response->getPayment();
            $status = $response->result;
            $note = $response->message ?: $payment->message;

            if ($status) {
                $result = true;

                if (in_array($action, $this->fraudTransactionTypes)) {
                    $this->setFraudCheckData($paymentTransaction, $payment);
                    if (BackendTransaction::TRAN_TYPE_DECLINE == $action) {
                        // Register refund/void that should've happened after decline
                        $this->registerBackendTransaction($paymentTransaction, $payment);
                    }
                }

                $transaction->setStatus(BackendTransaction::STATUS_SUCCESS);
                \XLite\Core\TopMessage::addInfo($note);
            } else {
                throw new ApiException($note ?: 'Operation failed');
            }

        } catch (ApiException $exception) {
            $result = false;
            $note = $exception->getMessage();
            $this->log('Error: ' . $note);
            // Show error because it is visible to admin only
            \XLite\Core\TopMessage::addError($note);
        }

        return $result;
    }

    /**
     * Get name for the transaction data cell
     *
     * @param string $title
     * @param string $prefix
     *
     * @return string
     */
    protected function getTransactionDataCellName($title, $prefix = '')
    {
        return $prefix . \XLite\Core\Converter::convertToCamelCase(
            preg_replace('/[^a-z0-9_-]+/i', '_', $title)
        );
    }

    /**
     * Sets all required transaction data cells for further operations
     *
     * @param \XLite\Model\Payment\Transaction $transaction
     * @param \XPaymentsCloud\Model\Payment $payment
     */
    protected function setTransactionDataCells(Transaction $transaction, XpPayment $payment)
    {
        $transaction->setXpaymentsId($payment->xpid);
        $transaction->setDataCell('xpaymentsMessage', $payment->lastTransaction->message, 'Message');

        $actions = [
            'capture' => 'Capture',
            'void' => 'Void',
            'refund' => 'Refund',
        ];

        foreach ($actions as $action => $cellName) {
            $can = ($payment->isTransactionSupported($action)) ? static::ACTION_ALLOWED : static::ACTION_NOTALLOWED;
            if (static::ACTION_ALLOWED == $can) {
                if ($payment->isTransactionSupported($action . 'Multi')) {
                    $can = static::ACTION_MULTI;
                } elseif ($payment->isTransactionSupported($action . 'Part')) {
                    $can = static::ACTION_PART;
                }
            }
            $transaction->setDataCell('xpayments' . $cellName, $can, $cellName);

        }

        if (is_object($payment->details)) {

            // Set payment details i.e. something that returned from the gateway

            $details = get_object_vars($payment->details);

            foreach ($details as $title => $value) {
                if (!empty($value) && !preg_match('/(\[Kount\]|\[NoFraud\]|\[Signifyd\])/i', $title)) {
                    $name = $this->getTransactionDataCellName($title, 'xpaymentsDetails.');
                    $transaction->setDataCell($name, $value, $title);
                }
            }
        }

        if (is_object($payment->verification)) {

            // Set verification (AVS and CVV) 

            if (!empty($payment->verification->avsRaw)) {
                $transaction->setDataCell('xpaymentsAvsResult', $payment->verification->avsRaw, 'AVS Check Result');
            }

            if (!empty($payment->verification->cvvRaw)) {
                $transaction->setDataCell('xpaymentsCvvResult', $payment->verification->cvvRaw, 'CVV Check Result');
            }
        }

        if (
            is_object($payment->card)
            && !empty($payment->card->last4)
        ) {

            // Set masked card details

            if (empty($payment->card->first6)) {
                $first6 = '******';
            } else {
                $first6 = $payment->card->first6;
            }

            $transaction->setDataCell(
                'xpaymentsCardNumber',
                sprintf('%s******%s', $first6, $payment->card->last4),
                'Card number',
                'C'
            );

            if (
                !empty($payment->card->expireMonth)
                && !empty($payment->card->expireYear)
            ) {

                $transaction->setDataCell(
                    'xpaymentsCardExpirationDate',
                    sprintf('%s/%s', $payment->card->expireMonth, $payment->card->expireYear),
                    'Expiration date',
                    'C'
                );
            }

            if (!empty($payment->card->type)) {
                $transaction->setDataCell(
                    'xpaymentsCardType',
                    $payment->card->type,
                    'Card type',
                    'C'
                );
            }

            if (!empty($payment->card->cardholderName)) {
                $transaction->setDataCell(
                    'xpaymentsCardholder',
                    $payment->card->cardholderName,
                    'Cardholder name',
                    'C'
                );
            }
        }
    }

    /**
     * Set initial payment transaction status by response status
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction to update
     * @param integer $responseStatus Transaction status from X-Payments
     *
     * @return void
     */
    protected function setTransactionTypeByStatus(Transaction $transaction, $responseStatus)
    {
        // Initial transaction type is not known currently before payment, try to guess it from X-P transaction status
        if (XpPayment::AUTH == $responseStatus) {
            $transaction->setType(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH);
        } elseif (XpPayment::CHARGED == $responseStatus) {
            $transaction->setType(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE);
        }
    }

    /*
     * Init SDK Client
     *
     * @return \XPaymentsCloud\Client
     */
    protected function initClient()
    {
        return ModuleMain::getClient();
    }

    /**
     * Disable Apple Pay method if disabled
     *
     * @param \XLite\Model\Payment\Method $method
     *
     * @return void
     */
    public function enableMethod(\XLite\Model\Payment\Method $method)
    {
        if (!$method->getEnabled()) {
            ModuleMain::getApplePayMethod()->setEnabled(false);
        }
    }

    /**
     * Get payment method input template
     *
     * @return string
     */
    public function getInputTemplate()
    {
        return 'modules/XPay/XPaymentsCloud/checkout/widget.twig';
    }

    /**
     * @return string
     */
    public function getSettingsWidget()
    {
        return '\XLite\Module\XPay\XPaymentsCloud\View\ConnectWidget';
    }

    /**
     * @return bool
     */
    public function useDefaultSettingsFormButton()
    {
        return false;
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
        $httpsEnabled = \XLite\Core\Config::getInstance()->Security->admin_security
            && \XLite\Core\Config::getInstance()->Security->customer_security;

        return parent::isConfigured($method)
            && $method->getSetting('account')
            && $method->getSetting('api_key')
            && $method->getSetting('secret_key')
            && $method->getSetting('widget_key')
            && $httpsEnabled;
    }

    /**
     * Process callback
     *
     * @param Transaction $transaction Callback-owner transaction
     *
     * @return void
     *
     * @throws \XLite\Core\Exception\PaymentProcessing\CallbackRequestError
     */
    public function processCallback(Transaction $transaction)
    {
        parent::processCallback($transaction);

        if ($transaction->isXpayments()) {
            $api = $this->initClient();

            try {
                $response = $api->parseCallback();
            } catch (\XPaymentsCloud\ApiException $exception) {
                throw new \XLite\Core\Exception\PaymentProcessing\CallbackRequestError($exception->getMessage());
            }

            $payment = $response->getPayment();
            $xpaymentsSubscription = $response->getSubscription();

            if (0 !== strcmp($transaction->getXpaymentsId(), $payment->xpid)) {
                // This is a rebill
                $parentTransaction = $transaction;
                $transaction = Database::getRepo('XLite\Model\Payment\Transaction')
                                        ->findOneByCell('xpaymentsPaymentId', $payment->xpid);
                if (!$transaction) {
                    $transaction = $this->createChildTransaction($parentTransaction, $payment, $xpaymentsSubscription);
                }
            }

            $this->setFraudCheckData($transaction, $payment);

            $this->registerBackendTransaction($transaction, $payment);

            if (
                $xpaymentsSubscription
                && \XPaymentsCloud\Model\Subscription::STATUS_ACTIVE === $xpaymentsSubscription->getStatus()
            ) {
                $transaction->setStatus(Transaction::STATUS_SUCCESS);
                $this->setTransactionTypeByStatus($transaction, $payment->status);

                switch ($payment->status) {
                    case XpPayment::AUTH:
                        $paymentStatus = \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED;
                        break;
                    case XpPayment::CHARGED:
                        $paymentStatus = \XLite\Model\Order\Status\Payment::STATUS_PAID;
                        break;
                    default:
                        $paymentStatus = \XLite\Model\Order\Status\Payment::STATUS_QUEUED;
                }

                $transaction->getOrder()->setPaymentStatus($paymentStatus);

                \XLite\Core\Mailer::sendXpaymentsSubscriptionPaymentSuccessful($transaction->getOrder(), $transaction->getOrder()->getXpaymentsSubscription());
            }

        } else {
            throw new \XLite\Core\Exception\PaymentProcessing\CallbackRequestError('Couldn\'t find an X-Payments Cloud payment for callback!');
        }
    }

    /**
     * Process return from 3-D Secure form and complete payment
     *
     * @param Transaction $transaction
     */
    public function processReturn(Transaction $transaction)
    {
        parent::processReturn($transaction);

        // Clear 3-D Secure data from session
        if (!empty(Session::getInstance()->xpaymentsData)) {
            unset(Session::getInstance()->xpaymentsData);
        }

        if ($transaction->isXpayments()) {

            $api = $this->initClient();

            try {
                $response = $api->doContinue(
                    $transaction->getXpaymentsId()
                );

                $payment = $response->getPayment();
                $note = $response->message ?: $payment->message;

                $result = $this->processPaymentFinish($transaction, $payment);

                $xpaymentsSubscriptions = $response->getSubscriptions();
                if ($xpaymentsSubscriptions) {
                    $this->createSubscriptions($xpaymentsSubscriptions);
                }


            } catch (\XPaymentsCloud\ApiException $exception) {
                $result = static::FAILED;
                $note = $exception->getMessage();
                $transaction->setDataCell('xpaymentsMessage', $note, 'Message');

                $this->handleApiException($exception, 'Failed to process the payment!');
            }

            $transaction->setNote($note);
            $transaction->setStatus($result);

        } else {
            // Invalid non-XP transaction
            TopMessage::addError('Transaction was lost!');
        }
    }

    /**
     * Finalize initial transaction
     *
     * @param Transaction $transaction
     * @param \XPaymentsCloud\Model\Payment $payment
     *
     * @return string
     */
    protected function processPaymentFinish(Transaction $transaction, XpPayment $payment)
    {
        $this->setTransactionDataCells($transaction, $payment);

        $this->setFraudCheckData($transaction, $payment);

        if ($payment->initialTransactionId) {
            $transaction->setPublicId($payment->initialTransactionId . ' (' . $transaction->getPublicId() . ')');
        }

        if ($payment->customerId) {
            $transaction->getOrigProfile()->setXpaymentsCustomerId($payment->customerId);
        }

        $status = $payment->status;

        if (
            XpPayment::AUTH == $status
            || XpPayment::CHARGED == $status
        ) {
            $result = static::COMPLETED;
            $this->setTransactionTypeByStatus($transaction, $status);

        } elseif (
            XpPayment::DECLINED == $status
        ) {
            $result = static::FAILED;

        } else {
            $result = static::PENDING;
        }

        return $result;
    }

    /**
     * Process Card Setup request
     *
     * @param string $token
     * @param \XLite\Model\Profile $profile
     * @param \XLite\Model\Address $address
     * @param string $returnUrl
     *
     * @return \XPaymentsCloud\Response
     */
    public function processCardSetup($token, \XLite\Model\Profile $profile, \XLite\Model\Address $address, $returnUrl)
    {
        $response = null;
        // Uses external client call because regular method will work only during checkout
        $client = \XLite\Module\XPay\XPaymentsCloud\Main::getClient();

        try {
            $response = $client->doTokenizeCard(
                $token,
                'Card Setup',
                $profile->getXpaymentsCustomerId(),
                $this->prepareCardSetupCart($profile, $address),
                $returnUrl,
                ''
            );

            if (is_null($response->redirectUrl)) {
                // 3-D Secure is not enabled, so we can finalize
                $this->processCardSetupFinalize($response->getPayment(), $profile);
            }

        } catch (\XPaymentsCloud\ApiException $exception) {
            $this->handleApiException($exception, 'Card setup has been failed!');
        }

        return $response;
    }

    /**
     * Continue Card Setup request after 3-D Secure
     *
     * @param string $xpid
     * @param \XLite\Model\Profile $profile
     *
     */
    public function processContinueCardSetup($xpid, \XLite\Model\Profile $profile)
    {
        // Uses external client call because regular method will work only during checkout
        $client = \XLite\Module\XPay\XPaymentsCloud\Main::getClient();

        try {
            $response = $client->doContinue($xpid);
            $this->processCardSetupFinalize($response->getPayment(), $profile);

        } catch (\XPaymentsCloud\ApiException $exception) {
            $this->handleApiException($exception, 'Card setup has been failed!');
        }

    }

    /**
     * Common actions to be executed after Card Setup
     *
     * @param XpPayment $payment
     * @param \XLite\Model\Profile $profile
     *
     * @throws ApiException
     */
    protected function processCardSetupFinalize(XpPayment $payment, \XLite\Model\Profile $profile)
    {
        if (!empty($payment->card->saved)) {
            TopMessage::addInfo('Card has been successfully saved');
            if ($payment->customerId) {
                $profile->setXpaymentsCustomerId($payment->customerId);
                Database::getEM()->flush();
            }
        } else {
            throw new \XPaymentsCloud\ApiException($payment->message);
        }
    }

    /**
     * Logs complete error message and show public message in front-end
     *
     * @param ApiException $exception
     * @param $defaultMessage
     */
    protected function handleApiException(\XPaymentsCloud\ApiException $exception, $defaultMessage)
    {
        $this->log('Error: ' . $exception->getMessage());
        $message = $exception->getPublicMessage();
        if (!$message) {
            $message = $defaultMessage;
        }
        TopMessage::addError($message);
    }


    /**
     * Prepare X-Payments Customer Id
     *
     * @return string
     */
    public function getXpaymentsCustomerId()
    {
        $profile = $this->transaction->getOrigProfile();
        return ($profile) ? $profile->getXpaymentsCustomerId() : '';
    }

    /**
     * Returns merchant (order department) email
     *
     * @return string
     */
    public function getMerchantEmail()
    {
        // Try modern serialized emails or fallback to plain string
        $emails = @unserialize(\XLite\Core\Config::getInstance()->Company->orders_department);
        $merchantEmail = (is_array($emails) && !empty($emails))
            ? array_shift($emails)
            : \XLite\Core\Config::getInstance()->Company->orders_department;

        return $merchantEmail;
    }

    /**
     * Returns login string to be used in cart data
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return string
     */
    public function getLoginForCart($profile)
    {
        return $profile->getLogin() . ' (User ID #' . $profile->getProfileId() . ')';
    }

    /**
     * Prepares cart to be used for Card Setup feature
     *
     * @param \XLite\Model\Profile $profile
     * @param \XLite\Model\Address $address
     *
     * @return array
     */
    public function prepareCardSetupCart($profile, $address)
    {
        $email = $profile->getLogin();

        $result = [
            'login' => $this->getLoginForCart($profile),
            'currency' => \XLite::getInstance()->getCurrency()->getCode(),
            'billingAddress' => $this->prepareAddress($address, $email),
            'merchantEmail' => $this->getMerchantEmail(),
        ];

        return $result;
    }

    /**
     * Prepare shopping cart data
     *
     * @return array
     */
    public function prepareCart()
    {
        $cart = $this->transaction->getOrder();

        $profile = $cart->getProfile();

        if ($cart->getOrderNumber()) {

            $description = 'Order #' . $cart->getOrderNumber();

        } else {

            $description = $this->getInvoiceDescription();
        }

        $result = array(
            'login'                => $this->getLoginForCart($profile),
            'items'                => array(),
            'currency'             => \XLite::getInstance()->getCurrency()->getCode(),
            'shippingCost'         => 0.00,
            'taxCost'              => 0.00,
            'discount'             => 0.00,
            'totalCost'            => 0.00,
            'description'          => $description,
            'merchantEmail'        => $this->getMerchantEmail(),

        );

        $billing = $profile->getBillingAddress();
        $shipping = $profile->getShippingAddress();
        $email = $profile->getLogin();

        if ($billing && $shipping) {

            $result['billingAddress'] = $this->prepareAddress($billing, $email);
            $result['shippingAddress'] = $this->prepareAddress($shipping, $email);

        } elseif ($billing) {

            $result['billingAddress'] = $result['shippingAddress'] = $this->prepareAddress($billing, $email);

        } else {

            $result['billingAddress'] = $result['shippingAddress'] = $this->prepareAddress($shipping, $email);
        }

        // Set items
        if ($cart->getItems()) {

            foreach ($cart->getItems() as $item) {

                $itemElement = array(
                    'sku'      => strval($item->getSku() ? $item->getSku() : $item->getName()),
                    'name'     => strval($item->getName() ? $item->getName() : $item->getSku()),
                    'price'    => $this->roundCurrency($item->getPrice()),
                    'quantity' => $item->getAmount(),
                );

                if ($item->isXpaymentsSubscription()) {

                    /* @var Plan $plan Subscription plan */
                    $plan = $item->getProduct()->getXpaymentsSubscriptionPlan();

                    $itemElement['isSubscription']   = true;
                    $itemElement['subscriptionPlan'] = [
                        'subscriptionSchedule' => [
                            'type'    => $plan->getType(),
                            'number'  => $plan->getNumber(),
                            'period'  => $plan->getPeriod(),
                            'reverse' => $plan->getReverse(),
                            'periods' => $plan->getPeriods(),
                        ],
                        'callbackUrl'          => Subscription::getCallbackUrl(),
                        'recurringAmount'      => $item->getAmount() * $item->getXpaymentsDisplayFeePrice(),
                        'description'          => $item->getDescription(),
                        'uniqueOrderItemId'    => $item->getXpaymentsUniqueId(),
                    ];
                } else {
                    $itemElement['isSubscription'] = false;
                }

                if (!$itemElement['sku']) {
                    $itemElement['sku'] = 'N/A';
                }

                if (!$itemElement['name']) {
                    $itemElement['name'] = 'N/A';
                }

                $result['items'][] = $itemElement;
            }
        }

        // Set costs
        $result['shippingCost'] = $this->roundCurrency(
            $cart->getSurchargesSubtotal(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, false)
        );
        $result['taxCost'] = $this->roundCurrency(
            $cart->getSurchargesSubtotal(\XLite\Model\Base\Surcharge::TYPE_TAX, false)
        );
        $result['totalCost'] = $this->roundCurrency($cart->getTotal());
        $result['discount'] = $this->roundCurrency(
            abs($cart->getSurchargesSubtotal(\XLite\Model\Base\Surcharge::TYPE_DISCOUNT, false))
        );

        return $result;
    }

    /**
     * @param float $amount
     * @param string $orderNumber
     * @return array
     */
    public function prepareRebillCart(float $amount, string $orderNumber)
    {
        $cart = $this->prepareCart();
        $cart['totalCost'] = $this->roundCurrency($amount);
        $cart['description'] = 'Extra charge for order #' . $orderNumber;

        return $cart;
    }

    /**
     * Prepare address data
     *
     * @param \XLite\Model\Profile $profile Customer's profile
     * @param $type Address type, billing or shipping
     *
     * @return array
     */
    protected function prepareAddress(\XLite\Model\Address $address, $email)
    {
        $result = array();

        $addressFields = array(
            'firstname' => 'N/A',
            'lastname'  => '',
            'address'   => 'N/A',
            'city'      => 'N/A',
            'state'     => 'N/A',
            'country'   => 'XX', // WA fix for MySQL 5.7 with strict mode
            'zipcode'   => 'N/A',
            'phone'     => '',
            'fax'       => '',
            'company'   => '',
        );

        $repo = Database::getRepo('\XLite\Model\AddressField');

        foreach ($addressFields as $field => $defValue) {

            $method = 'address' == $field ? 'street' : $field;

            if (
                $address
                && ($repo->findOneBy(array('serviceName' => $method)) || method_exists($address, 'get' . $method))
                && $address->$method
            ) {
                $result[$field] = $address->$method;
                if (is_object($result[$field])) {
                    $result[$field] = $result[$field]->getCode();
                }
            }

            if (empty($result[$field])) {
                $result[$field] = $defValue;
            }
        }

        $result['email'] = $email;

        return $result;
    }

    /**
     * Round currency
     *
     * @param float $data Data
     *
     * @return float
     */
    protected function roundCurrency($data)
    {
        return sprintf('%01.2f', round($data, 2));
    }

    /**
     * Create transaction by parent one and data passed from X-Payments Cloud
     *
     * @param \XLite\Model\Payment\Transaction $parentTransaction Parent transaction
     * @param \XPaymentsCloud\Model\Payment $payment Payment from X-Payments
     * @param XpaymentsSubscription $xpaymentsSubscription
     *
     * @return \XLite\Model\Payment\Transaction
     *
     * @throws \Exception
     */
    protected function createChildTransaction($parentTransaction, XpPayment $payment, XpaymentsSubscription $xpaymentsSubscription = null)
    {
        if ($xpaymentsSubscription) {
            /** @var Subscription $subscription */
            $subscription = Database::getRepo('XLite\Module\XPay\XPaymentsCloud\Model\Subscription\Subscription')
                ->findOneBy(['xpaymentsSubscriptionPublicId' => $xpaymentsSubscription->getPublicId()]);

            if (!$subscription) {
                /** @var \XLite\Model\OrderItem $initialItem */
                $initialItem = Database::getRepo('XLite\Model\OrderItem')
                    ->findOneBy(['xpaymentsUniqueId' => $xpaymentsSubscription->getUniqueOrderItemId()]);
                if ($initialItem) {
                    $subscription = new Subscription();
                    $initialItem->setXpaymentsSubscription($subscription);
                    $subscription->setInitialOrderItem($initialItem)
                        ->setShippingId($initialItem->getOrder()->getShippingId())
                        ->setShippingAddress($initialItem->getOrder()->getProfile()->getShippingAddress())
                        ->setCalculateShipping($initialItem->getProduct()->getXpaymentsSubscriptionPlan()->getCalculateShipping())
                        ->createOrUpdateFromXpaymentsSubscription($xpaymentsSubscription);
                    Database::getEM()->persist($subscription);
                    Database::getEM()->flush();
                }
            }

            if ($subscription) {
                $cart = $subscription->createOrder();
            }
        }

        if (!$cart) {
            $parentOrder = $parentTransaction->getOrder();
            $cart = $this->createCart(
                $parentOrder->getOrigProfile(),
                $parentTransaction->getPaymentMethod(),
                $payment->amount,
                $payment->description ?: 'Extra charges',
                're-bill'
            );
        }

        $transaction = $cart->getFirstOpenPaymentTransaction();

        if ($transaction) {
            $this->processPaymentFinish($transaction, $payment);
        }

        if (XpPayment::INITIALIZED != $payment->status) {
            $transaction->setStatus(Transaction::STATUS_SUCCESS);

            switch ($payment->status) {
                case XpPayment::AUTH:
                    $paymentStatus = \XLite\Model\Order\Status\Payment::STATUS_AUTHORIZED;
                    break;
                case XpPayment::CHARGED:
                    $paymentStatus = \XLite\Model\Order\Status\Payment::STATUS_PAID;
                    break;
                default:
                    $paymentStatus = \XLite\Model\Order\Status\Payment::STATUS_QUEUED;
            }

            $cart->setPaymentStatus($paymentStatus);
        }
        $cart->processSucceed();

        return $transaction;
    }

    /**
     * Create a cart with non existing item with required total
     *
     * @param \XLite\Model\Profile $profile Customer's profile for whom the cart is created for
     * @param \XLite\Model\Payment\Method $paymentMethod Payment methood
     * @param float $total Cart total
     * @param string $itemName Name of the fake item
     * @param string $itemSku SKU of the fake item
     *
     * @return \XLite\Model\Cart
     *
     */
    public function createCart(\XLite\Model\Profile $profile, \XLite\Model\Payment\Method $paymentMethod, $total, $itemName, $itemSku)
    {
        $cart = new \XLite\Model\Cart;

        $cart->setTotal($total);
        $cart->setSubtotal($total);
        $cart->setCurrency(\XLite::getInstance()->getCurrency());
        $cart->setDate(time());
        Database::getEM()->persist($cart);
        Database::getEM()->flush();

        $cart->setOrderNumber(Database::getRepo('XLite\Model\Order')->findNextOrderNumber());
        $cart->setProfileCopy($profile);
        $cart->setLastShippingId(null);
        $cart->setPaymentMethod($paymentMethod, $total);

        $item = new \XLite\Model\OrderItem;
        $item->setName($itemName);
        $item->setSku($itemSku);
        $item->setPrice($total);
        $item->setAmount(1);
        $item->setTotal($total);
        $item->setSubtotal($total);
        $item->setDiscountedSubtotal($total);
        $item->setXpaymentsEmulated(true);

        Database::getEM()->persist($item);

        $cart->addItem($item);

        Database::getEM()->flush();

        return $cart;
    }

    /**
     * Register backend transaction
     *
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     * @param \XPaymentsCloud\Model\Payment $payment
     *
     * @return void
     */
    protected function registerBackendTransaction(Transaction $transaction, XpPayment $payment)
    {
        $type = null;
        $value = null;

        switch ($payment->status) {
            case XpPayment::INITIALIZED:
                $type = BackendTransaction::TRAN_TYPE_SALE;
                break;

            case XpPayment::AUTH:
                $type = BackendTransaction::TRAN_TYPE_AUTH;
                break;

            case XpPayment::DECLINED:
                if (0 == $payment->authorized->amount && 0 == $payment->charged->amount) {
                    $type = BackendTransaction::TRAN_TYPE_DECLINE;
                } else {
                    $type = BackendTransaction::TRAN_TYPE_VOID;
                }
                break;

            case XpPayment::CHARGED:
                if ($payment->amount == $payment->charged->amount) {
                    $type = BackendTransaction::TRAN_TYPE_CAPTURE;
                    $value = $this->getActualAmount('captured', $transaction, $payment->amount);
                } else {
                    $type = BackendTransaction::TRAN_TYPE_CAPTURE_PART;
                    $value = $this->getActualAmount('captured', $transaction, $payment->amount);
                }
                break;

            case XpPayment::REFUNDED:
                $type = BackendTransaction::TRAN_TYPE_REFUND;
                $value = $this->getActualAmount('refunded', $transaction, $payment->amount);
                break;

            case XpPayment::PART_REFUNDED:
                $type = BackendTransaction::TRAN_TYPE_REFUND_PART;
                $value = $this->getActualAmount('refunded', $transaction, $payment->amount);
                break;

            default:

        }

        if ($type) {
            $backendTransaction = $transaction->createBackendTransaction($type);
            if (XpPayment::INITIALIZED != $payment->status) {
                $backendTransaction->setStatus(BackendTransaction::STATUS_SUCCESS);
            }
            if (0.01 <= $value) {
                $backendTransaction->setValue($value);
            }
            $backendTransaction->setDataCell('xpaymentsMessage', $payment->lastTransaction->message, 'Message');
            $backendTransaction->registerTransactionInOrderHistory('callback');
        }
    }

    /**
     * Get transaction refunded amount
     *
     * @param string $action 'refunded' or 'captured'
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     * @param float $baseAmount Amount received in callback
     *
     * @return float
     */
    protected function getActualAmount($action, Transaction $transaction, $baseAmount)
    {
        $amount = 0;
        $btTypes = [];
        if ('refunded' == $action) {
            $btTypes = [
                BackendTransaction::TRAN_TYPE_REFUND,
                BackendTransaction::TRAN_TYPE_REFUND_PART,
                BackendTransaction::TRAN_TYPE_REFUND_MULTI,
            ];
        } elseif ('captured' == $action) {
            $btTypes = [
                BackendTransaction::TRAN_TYPE_CAPTURE,
                BackendTransaction::TRAN_TYPE_CAPTURE_PART,
                BackendTransaction::TRAN_TYPE_CAPTURE_MULTI,
            ];
        }

        foreach ($transaction->getBackendTransactions() as $bt) {
            if ($bt->isCompleted() && in_array($bt->getType(), $btTypes)) {
                $amount += $bt->getValue();
            }
        }

        $amount = $baseAmount - $amount;

        return max(0, $amount);
    }

    /**
     * Get admin URL of X-Payments
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getXpaymentsAdminUrl()
    {
        return $this->initClient()->getAdminUrl();
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
     * Wrapper for logger
     *
     * @param $message
     */
    protected function log($message)
    {
        \XLite\Module\XPay\XPaymentsCloud\Main::log($message);
    }

    /**
     * Redirect customer to X-Payments (for 3-d Secure)
     *
     * @param string $url Url
     *
     * @return void
     */
    protected function redirectToPay($url)
    {
        $url = str_replace('\'', '', \Includes\Utils\Converter::removeCRLF($url));
        $page = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body onload="self.location = '$url';">
</body>
</html>
HTML;

        echo $page;
    }

    /**
     * Parse fraud check results and set corresponding XpaymentsFraudCheckData
     *
     * @param Transaction $transaction
     * @param XpPayment $payment
     *
     * @return void
     */
    protected function setFraudCheckData(Transaction $transaction, XpPayment $payment)
    {
        if (!$payment->fraudCheck) {
            return;
        }

        $oldFraudCheckData = $transaction->getXpaymentsFraudCheckData();
        if ($oldFraudCheckData) {
            foreach ($transaction->getXpaymentsFraudCheckData() as $fraudCheckData) {
                Database::getEM()->remove($fraudCheckData);
            }
        }

        // Maximum fraud result within several services (if there are more than one)
        $maxFraudResult = FraudCheckData::RESULT_UNKNOWN;

        // Code of the service which got "most fraud" result
        $maxFraudResultCode = '';

        // Flag to check if any errors which prevented fraud check occurred
        $errorsFound = false;

        foreach ($payment->fraudCheck as $service) {

            // Ignore "noname" services. This must be filled in on the X-Payments Cloud side
            if (!$service['code'] || !$service['service']) {
                continue;
            }

            if (!$maxFraudResultCode) {
                // Use first the code, so that something is specified
                $maxFraudResultCode = $service['code'];
            }

            $fraudCheckData = new FraudCheckData;
            $fraudCheckData->setTransaction($transaction);

            $transaction->addXpaymentsFraudCheckData($fraudCheckData);

            $fraudCheckData->setCode($service['code']);
            $fraudCheckData->setService($service['service']);

            $module = $service['module'] ?? '';

            $fraudCheckData->setModule($module);

            if (!empty($service['result'])) {
                $fraudCheckData->setResult($service['result']);

                if (intval($service['result']) > $maxFraudResult) {
                    $maxFraudResult = intval($service['result']);
                    $maxFraudResultCode = $service['code'];
                }
            }

            if (!empty($service['status'])) {
                $fraudCheckData->setStatus($service['status']);
            }

            if (!empty($service['score'])) {
                $fraudCheckData->setScore($service['score']);
            }

            if (!empty($service['transactionId'])) {
                $fraudCheckData->setServiceTransactionId($service['transactionId']);
            }

            if (!empty($service['url'])) {
                $fraudCheckData->setUrl($service['url']);
            }

            if (!empty($service['message'])) {
                $fraudCheckData->setMessage($service['message']);

                if (FraudCheckData::RESULT_UNKNOWN == $service['result']) {
                    // Unknown result with message should be shown as error
                    $errorsFound = true;
                }
            }

            if (!empty($service['errors'])) {
                $errors = implode("\n", $service['errors']);
                $fraudCheckData->setErrors($errors);
                $errorsFound = true;
            }

            if (!empty($service['rules'])) {
                $rules = implode("\n", $service['rules']);
                $fraudCheckData->setRules($rules);
            }

            if (!empty($service['warnings'])) {
                $warnings = implode("\n", $service['warnings']);
                $fraudCheckData->setWarnings($warnings);
            }
        }

        // Convert maximum fraud result to the order's fraud status
        $status = Order::FRAUD_STATUS_UNKNOWN;
        switch ($maxFraudResult) {

            case FraudCheckData::RESULT_UNKNOWN:
                if ($errorsFound) {
                    $status = Order::FRAUD_STATUS_ERROR;
                } else {
                    $status = Order::FRAUD_STATUS_UNKNOWN;
                }
                break;

            case FraudCheckData::RESULT_ACCEPTED:
                $status = Order::FRAUD_STATUS_CLEAN;
                break;

            case FraudCheckData::RESULT_MANUAL:
            case FraudCheckData::RESULT_PENDING:
                $status = Order::FRAUD_STATUS_REVIEW;
                break;

            case FraudCheckData::RESULT_FAIL:
                $status = Order::FRAUD_STATUS_FRAUD;
                break;
        }

        $transaction->getOrder()
            ->setXpaymentsFraudStatus($status)
            ->setXpaymentsFraudType($maxFraudResultCode)
            ->setXpaymentsFraudCheckTransactionId($transaction->getTransactionId());
    }

    /**
     * Get cart
     *
     * @return \XLite\Model\Cart
     */
    protected function getCart()
    {
        return $this->transaction
            ? $this->transaction->getOrder()
            : \XLite\Model\Cart::getInstance();
    }

}
