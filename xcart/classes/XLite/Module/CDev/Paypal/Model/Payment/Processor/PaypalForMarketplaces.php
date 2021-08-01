<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Model\Payment\Processor;

use XLite\Core\TopMessage;
use XLite\Model\Payment\BackendTransaction;
use XLite\Model\Payment\Transaction;
use XLite\Module\CDev\Paypal\Core\Api\Orders\Order;
use XLite\Module\CDev\Paypal\Core\Api\Orders\PurchaseUnit;
use XLite\Module\CDev\Paypal\Core\Api\ReferencedPayouts\ReferencedPayoutsItem;
use XLite\Module\CDev\Paypal\Core\PaypalForMarketplacesAPI;

class PaypalForMarketplaces extends \XLite\Module\CDev\Paypal\Model\Payment\Processor\ExpressCheckout
{
    protected $knowledgeBasePageURL = 'https://developer.paypal.com/docs/marketplaces/register-marketplaces/';

    /**
     * Constructor
     */
    public function __construct()
    {
        $method = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(
            \XLite\Module\CDev\Paypal\Main::PP_METHOD_PFM
        );

        $mode = $method->getSetting('mode') === \XLite\View\FormField\Select\TestLiveMode::TEST ? 'sandbox' : 'live';

        $this->api = new PaypalForMarketplacesAPI([
            'email'                  => $method->getSetting('email'),
            'client_id'              => $method->getSetting('client_id'),
            'secret'                 => $method->getSetting('secret'),
            'partner_id'             => $method->getSetting('partner_id'),
            'bn_code'                => $method->getSetting('bn_code'),
            'mode'                   => $mode,
            'additional_merchant_id' => $method->getSetting('additional_merchant_id'),
            'payment_descriptor'     => $method->getSetting('payment_descriptor'),
        ]);
    }

    /**
     * Returns the list of settings available for this payment processor
     *
     * @return array
     */
    public function getAvailableSettings()
    {
        return array(
            'disburse_funds_option_locked',
        );
    }

    /**
     * Get transactionId-based callback URL
     *
     * @param string  $fieldName TransactionId field name OPTIONAL
     * @param boolean $withId    Add to URL transaction id or not OPTIONAL
     *
     * @return string
     */
    protected function getCallbackURL($fieldName = self::RETURN_TXN_ID, $withId = false)
    {
        $query = [
            'txn_id_name' => $fieldName ?: self::RETURN_TXN_ID,
        ];

        if ($withId) {
            $query[$query['txn_id_name']] = $this->transaction->getPublicTxnId();
        }

        return \XLite::getInstance()->getShopURL(
            \XLite\Core\Converter::buildURL('callback', '', $query, \XLite::CART_SELF),
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
        return 'https://www.paypal.com/us/webapps/mpp/partner-program/contact-us?ref=marketplace';
    }

    /**
     * Get URL of referral page
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getPartnerPageURL(\XLite\Model\Payment\Method $method)
    {
        return 'https://www.paypal.com/us/selfhelp/article/what-is-paypal-for-marketplaces-ts2122';
    }

    /**
     * Get input template
     *
     * @return string
     */
    public function getInputTemplate()
    {
        return 'modules/CDev/Paypal/checkout/paypal_for_marketplaces_checkout_box.twig';
    }

    /**
     * Get allowed backend transactions
     *
     * @return string[] Statuses
     */
    public function getAllowedTransactions()
    {
        return [
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_PAYOUT,
        ];
    }

    /**
     * @param \XLite\Model\Payment\Transaction $transaction
     *
     * @return boolean
     */
    public function isRefundTransactionAllowed($transaction)
    {
        return $transaction->isRefundMultiTransactionAllowed();
    }

    /**
     * @param \XLite\Model\Payment\Transaction $transaction
     *
     * @return boolean
     */
    public function isPayoutTransactionAllowed($transaction)
    {
        if (!\XLite\Core\Auth::getInstance()->isAdmin()) {

            return false;
        }

        $disburseFunds = $transaction->getDataCell('disburse_funds')
            ? $transaction->getDataCell('disburse_funds')->getValue()
            : 'DELAYED';

        if ($disburseFunds !== 'DELAYED') {
            return false;
        }

        /** @var \XLite\Model\Payment\BackendTransaction[] $backendTransactions */
        $backendTransactions = $transaction->getBackendTransactions();
        foreach ($backendTransactions as $backendTransaction) {
            if ($backendTransaction->getType() !== \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE) {
                continue;
            }

            $captureId            = $backendTransaction->getDataCell('capture_id');
            $payee                = $backendTransaction->getDataCell('payee');
            $payoutId             = $backendTransaction->getDataCell('payout_transaction_id');
            $purchaseUnitRefunded = $backendTransaction->getDataCell('purchase_unit_refunded');

            if ($captureId && $payee && !$payoutId && !$purchaseUnitRefunded) {
                return true;
            }
        }

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
        return \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE;
    }

    /**
     * Get the list of merchant countries where this payment processor can work
     *
     * @return array
     */
    public function getAllowedMerchantCountries()
    {
        return [];
    }

    /**
     * @param \XLite\Model\Payment\Method $method
     * @param \XLite\Model\Cart           $cart
     *
     * @return bool
     */
    public function isCheckoutAvailable($method, $cart)
    {
        return $method->getSetting('additional_merchant_id');
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
        return $this->api->isConfigured();
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
                        'CHECKOUT.ORDER.PROCESSED',
                        'PAYMENT.CAPTURE.COMPLETED',
                        'PAYMENT.REFERENCED-PAYOUT-ITEM.COMPLETED',
                        'PAYMENT.CAPTURE.REFUNDED',
                        'PAYMENT.CAPTURE.DENIED',
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
     * Create order (orderId is used as token)
     *
     * @param \XLite\Model\Payment\Method           $method Payment method
     * @param \XLite\Model\Payment\Transaction|null $transaction
     *
     * @return string
     */
    public function doSetExpressCheckout(\XLite\Model\Payment\Method $method, \XLite\Model\Payment\Transaction $transaction = null)
    {
        $token             = null;
        $this->transaction = $transaction;

        try {
            $order = $this->api->createOrder(
                $this->transaction,
                $this->getPaymentCancelUrl(),
                $this->getPaymentReturnUrl(),
                $this->getCallbackURL(null, true) // IPN Notification URL
            );

            $token = $order->getId();

        } catch (\PayPal\Exception\PayPalConnectionException $ppException) {
            $token = null;

            $errorMessages = [];
            $exData = json_decode($ppException->getData(), true);
            if (isset($exData['details'])) {
                foreach ($exData['details'] as $exDetail) {
                    if (isset($exDetail['issue'])) {
                        $errorMessages[] = $exDetail['issue'];
                    }
                }
            }

            $this->errorMessage = $errorMessages
                ? implode(', ', $errorMessages)
                : static::t('An error occurred, please try again. If the problem persists, contact the administrator.');
        }

        return $token;
    }

    /**
     * doGetExpressCheckoutDetails
     *
     * @param \XLite\Model\Payment\Method $method Payment method object
     *
     * @return array
     */
    public function doGetExpressCheckoutDetails(\XLite\Model\Payment\Method $method)
    {
        $data = [];

        $orderId = \XLite\Core\Request::getInstance()->token;

        $order = $this->api->getOrder($orderId);
        if ($order) {
            $data['order'] = $order;
            $data['EMAIL'] = $order->getPayerInfo()->getEmail();
        }

        return $data;
    }

    /**
     * Translate array of data received from Paypal to the array for updating cart
     *
     * @param array $paypalData Array of customer data received from Paypal
     *
     * @return array
     */
    public function prepareBuyerData($paypalData)
    {
        /** @var \XLite\Module\CDev\Paypal\Core\Api\Orders\Order $order */
        $order           = $paypalData['order'];
        $shippingAddress = $order->getPurchaseUnits()[0]->getShippingAddress();

        $country = \XLite\Core\Database::getRepo('XLite\Model\Country')
            ->findOneByCode($shippingAddress->getCountryCode());

        $stateCode = \Includes\Utils\ArrayManager::getIndex($paypalData, 'SHIPTOSTATE', true);
        $state     = ($country && $stateCode)
            ? \XLite\Core\Database::getRepo('XLite\Model\State')
                ->findOneByCountryAndState($country->getCode(), $shippingAddress->getState())
            : null;

        $name = implode(
            ' ',
            array_filter([
                $order->getPayerInfo()->getFirstName(),
                $order->getPayerInfo()->getMiddleName(),
                $order->getPayerInfo()->getLastName(),
            ])
        );

        $street = implode(
            ' ',
            array_filter([
                $shippingAddress->getLine1(),
                $shippingAddress->getLine2(),
            ])
        );

        $data = [
            'shippingAddress' => [
                'name'    => $name,
                'street'  => $street,
                'country' => $country ?: '',
                'state'   => $state,
                'city'    => $shippingAddress->getCity(),
                'zipcode' => $shippingAddress->getPostalCode(),
                'phone'   => $shippingAddress->getPhone(),
            ],
        ];

        return $data;
    }

    /**
     * Perform 'DoExpressCheckoutPayment' request and return status of payment transaction
     *
     * @return string
     */
    protected function doDoExpressCheckoutPayment()
    {
        $status = self::FAILED;

        $transaction       = $this->transaction;
        $transactionStatus = $transaction::STATUS_FAILED;

        $request = \XLite\Core\Request::getInstance();

        $token = $request->token ?: \XLite\Core\Session::getInstance()->ec_token;

        $disburseFunds = $this->getSetting('disburse_funds') ?: 'DELAYED';
        try {
            $payOrderResponse = $this->api->getPayOrder($token, $disburseFunds);
            $transaction->setDataCell('disburse_funds', $disburseFunds, 'Disburse Funds');
        } catch (\Exception $e) {
            $payOrderResponse = null;
        }

        if ($payOrderResponse) {
            $status = self::COMPLETED;

        } else {
            $this->setDetail(
                'status',
                'Failed: unexpected response received from PayPal',
                'Status'
            );

            $transaction->setNote('Unexpected response received from PayPal');
        }

        if ($status === self::COMPLETED
            && $orderData = $this->api->getOrder($token)
        ) {
            foreach ($orderData->getPurchaseUnits() as $purchaseUnit) {
                $bt = $this->createBackendTransactionForPurchaseUnit($purchaseUnit, $transaction, $orderData);

                if ($bt->getStatus() === $transaction::STATUS_PENDING) {
                    $status = self::PENDING;
                }
            }

            $this->setDetail('order_reference_id', $orderData->getId(), 'Unique PayPal order ID');
            $this->setDetail('payer_id', $orderData->getPayerInfo()->getPayerId(), 'Unique customer ID');
            $this->setDetail('payment_id', $orderData->getPaymentDetails()->getPaymentId(), 'Unique PayPal payment ID');

        } else {
            $transaction->setStatus($transactionStatus);

            $this->updateInitialBackendTransaction($transaction, $transactionStatus);
        }

        \XLite\Core\Session::getInstance()->ec_token    = null;
        \XLite\Core\Session::getInstance()->ec_date     = null;
        \XLite\Core\Session::getInstance()->ec_payer_id = null;
        \XLite\Core\Session::getInstance()->ec_type     = null;

        return $status;
    }

    /**
     * @param PurchaseUnit $purchaseUnit
     * @param Transaction  $transaction
     * @param Order        $orderData
     *
     * @return BackendTransaction
     */
    protected function createBackendTransactionForPurchaseUnit($purchaseUnit, $transaction, $orderData)
    {
        $bt = $transaction->createBackendTransaction(BackendTransaction::TRAN_TYPE_SALE);
        $bt->setValue($purchaseUnit->getAmount()->getTotal());
        $bt->setDataCell('order_reference_id', $orderData->getId(), 'Unique PayPal order ID');
        $bt->setDataCell('transaction_reference_id', $purchaseUnit->getReferenceId(), 'Transaction public ID');

        $captures = $purchaseUnit->getPaymentSummary()->getCaptures();
        if ($captures) {
            $capture = $captures[0];

            $bt->setDataCell('capture_id', $capture->getId(), 'Capture ID');
            $bt->setDataCell('payee', $purchaseUnit->getPayee()->getEmail(), 'Payee');

            $partnerFee = $purchaseUnit->getPartnerFeeDetails()
                ? $purchaseUnit->getPartnerFeeDetails()->getAmount()->getValue()
                : 0;

            $payout         = $capture->getAmount()->getTotal()
                - $capture->getTransactionFee()->getValue()
                - $partnerFee;
            $payoutCurrency = $capture->getAmount()->getCurrency();

            $bt->setDataCell('payout_amount', $payout, 'Payout amount');
            $bt->setDataCell('payout_currency', $payoutCurrency, 'Payout currency');
        }

        $transactionStatus = $transaction::STATUS_FAILED;

        if (in_array($purchaseUnit->getStatus(), ['APPROVED', 'COMPLETED', 'IN_PROGRESS', 'PENDING'], true)) {
            $transactionStatus = $transaction::STATUS_PENDING;

        } elseif ($purchaseUnit->getStatus() === 'CAPTURED') {
            $transactionStatus = $transaction::STATUS_SUCCESS;

        } else {
            $this->setDetail(
                'status',
                'Failed: ' . $purchaseUnit->getStatus(),
                'Status',
                $bt
            );
        }

        $bt->setStatus($transactionStatus);

        return $bt;
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
        $order = null;

        $paymentTransaction = $transaction->getPaymentTransaction();
        /** @var BackendTransaction $backendTransaction */
        foreach ($paymentTransaction->getBackendTransactions() as $backendTransaction) {
            if ($backendTransaction->getType() !== $backendTransaction::TRAN_TYPE_SALE
                || $backendTransaction->getStatus() !== $backendTransaction::STATUS_SUCCESS) {
                continue;
            }

            if (null === $order) {
                $orderReference = $backendTransaction->getDataCell('order_reference_id')->getValue();
                $order          = $this->api->getOrder($orderReference);
            }

            $purchaseUnit = null;
            if ($order) {
                $referenceId = $backendTransaction->getDataCell('transaction_reference_id')->getValue();
                foreach ($order->getPurchaseUnits() as $unit) {
                    if ($referenceId === $unit->getReferenceId()) {
                        $purchaseUnit = $unit;
                        break;
                    }
                }
            }

            $captures = $purchaseUnit && $purchaseUnit->getPaymentSummary() && $purchaseUnit->getPaymentSummary()->getCaptures()
                ? $purchaseUnit->getPaymentSummary()->getCaptures()
                : [];

            foreach ($captures as $capture) {
                if ($capture->getStatus() === 'REFUNDED') {
                    continue;
                }

                try {
                    $refund = $this->api->refundCapture(
                        $capture,
                        $purchaseUnit
                    );
                } catch (\PayPal\Exception\PayPalConnectionException $e) {
                    $data = json_decode($e->getData(), true);

                    TopMessage::addWarning('Payment refund error: {{error}}', ['error' => $data['message']]);
                }

                if ($refund) {
                    $bt = $paymentTransaction->createBackendTransaction(BackendTransaction::TRAN_TYPE_REFUND_MULTI);
                    $bt->setValue($refund->getAmount()->getTotal());
                    $bt->setStatus(BackendTransaction::STATUS_SUCCESS);
                    $bt->setPaymentTransaction($paymentTransaction);

                    \XLite\Core\Database::getEM()->persist($bt);

                    $backendTransaction->setDataCell('purchase_unit_refunded', true, 'Is PurchaseUnit refunded');
                }
            }
        }

        \XLite\Core\Database::getEM()->flush();

        return true;
    }

    /**
     * Refund
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Backend transaction
     *
     * @return boolean
     */
    protected function doPayout(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $paymentTransaction = $transaction->getPaymentTransaction();

        $disburseFunds = $paymentTransaction->getDataCell('disburse_funds')
            ? $paymentTransaction->getDataCell('disburse_funds')->getValue()
            : 'DELAYED';

        if ($disburseFunds !== 'DELAYED') {

            return false;
        }

        $requestCaptureId = \XLite\Core\Request::getInstance()->capture_id;

        $captureTransaction = null;

        /** @var \XLite\Model\Payment\BackendTransaction[] $backendTransactions */
        $backendTransactions = $paymentTransaction->getBackendTransactions();
        foreach ($backendTransactions as $backendTransaction) {
            if ($backendTransaction->getType() !== BackendTransaction::TRAN_TYPE_SALE) {
                continue;
            }

            $captureId = $backendTransaction->getDataCell('capture_id');
            $payee     = $backendTransaction->getDataCell('payee');
            $payoutId  = $backendTransaction->getDataCell('payout_transaction_id');

            if ($captureId && $payee && !$payoutId && $captureId->getValue() === $requestCaptureId) {
                $captureTransaction = $backendTransaction;
                break;
            }
        }

        if ($captureTransaction) {
            try {
                $referencedPayoutsItem = $this->api->createReferencedPayoutsItem($captureId->getValue());

                if ($referencedPayoutsItem && $referencedPayoutsItem->getProcessingState()->getStatus() === 'SUCCESS') {
                    $transaction->setStatus(BackendTransaction::STATUS_SUCCESS);
                    $transaction->setValue($referencedPayoutsItem->getPayoutAmount()->getValue());
                    $transaction->setDataCell(
                        'payout_transaction_id',
                        $referencedPayoutsItem->getPayoutTransactionId(),
                        'Payout transaction ID'
                    );
                    $captureTransaction->setDataCell(
                        'payout_transaction_id',
                        $referencedPayoutsItem->getPayoutTransactionId(),
                        'Payout transaction ID'
                    );
                }

            } catch (\Exception $e) {
            }
        }

        \XLite\Core\Database::getEM()->flush();

        return true;
    }

    /**
     * Get callback owner transaction or null
     *
     * @return Transaction
     */
    public function getCallbackOwnerTransaction()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if ($data && isset($data['resource_type']) && $data['resource_type'] === 'checkout-order') {
            $order               = new Order($data['resource']);
            $transactionPublicId = null;
            foreach ($order->getPurchaseUnits() as $purchaseUnit) {
                $transactionPublicId = $purchaseUnit->getInvoiceNumber();
                break;
            }

            return \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')->findOneBy([
                'public_id' => $transactionPublicId,
            ]);
        }

        return null;
    }

    /**
     * Process callback
     *
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     */
    public function processCallback(\XLite\Model\Payment\Transaction $transaction)
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $this->transaction = $transaction;
        $this->logCallback((array) $data);

        $orderTotal = $transaction->getOrder()->getTotal();
        $total      = $orderTotal;
        if ($data && isset($data['resource_type']) && $data['resource_type'] === 'checkout-order') {
            $order = new Order($data['resource']);
            foreach ($order->getPurchaseUnits() as $purchaseUnit) {
                $bt = $this->getBackendTransaction($transaction, $purchaseUnit->getReferenceId());
                if ($purchaseUnit->getStatus() === 'CAPTURED') {
                    $total -= $bt->getValue();
                    $bt->setStatus(BackendTransaction::STATUS_SUCCESS);

                    $captures = $purchaseUnit->getPaymentSummary()->getCaptures();
                    if ($captures) {
                        $capture = $captures[0];

                        $bt->setDataCell('capture_id', $capture->getId(), 'Capture ID');
                        $bt->setDataCell('payee', $purchaseUnit->getPayee()->getEmail(), 'Payee');

                        $partnerFee = $purchaseUnit->getPartnerFeeDetails()
                            ? $purchaseUnit->getPartnerFeeDetails()->getAmount()->getValue()
                            : 0;

                        $payout         = $capture->getAmount()->getTotal()
                            - $capture->getTransactionFee()->getValue()
                            - $partnerFee;
                        $payoutCurrency = $capture->getAmount()->getCurrency();

                        $bt->setDataCell('payout_amount', $payout, 'Payout amount');
                        $bt->setDataCell('payout_currency', $payoutCurrency, 'Payout currency');
                    }

                } elseif ($purchaseUnit->getStatus() === 'VOIDED') {
                    $bt->setStatus(BackendTransaction::STATUS_FAILED);
                }

                $bt->registerTransactionInOrderHistory('webhook checkout-order');
            }

            if ($transaction->getCurrency()->roundValue($total) <= 0) {
                $transaction->setValue($orderTotal);
                $transaction->setStatus($transaction::STATUS_SUCCESS);
            } elseif ($total < $orderTotal) {
                $transaction->setValue($total);
                $transaction->setStatus($transaction::STATUS_SUCCESS);
            } else {
                $transaction->setStatus($transaction::STATUS_FAILED);
            }

            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * @param \XLite\Model\Payment\Transaction $transaction
     * @param                                  $publicId
     *
     * @return \XLite\Model\Payment\BackendTransaction
     */
    protected function getBackendTransaction($transaction, $publicId)
    {
        /** @var BackendTransaction $bt */
        foreach ($transaction->getBackendTransactions() as $bt) {
            $cell = $bt->getDataCell('transaction_reference_id');
            if ($cell && $cell->getValue() === $publicId) {
                return $bt;
            }
        }

        return null;
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
            case 'multi-vendor':
                return static::t('To enable this payment method, you need Multi-vendor module installed.');
            case 'https':
                return static::t(
                    'Payments with this payment method are not allowed because HTTPS is not configured',
                    [
                        'url' => \XLite\Core\Converter::buildURL('https_settings'),
                    ]
                );
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
        if (\XLite\Module\CDev\Paypal\Main::PP_METHOD_PFM === $method->getServiceName()
            && $this->api->isSelfConfigured()
            && !\Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('XC\MultiVendor')
        ) {
            return 'multi-vendor';
        }

        if (\XLite\Module\CDev\Paypal\Main::PP_METHOD_PFM === $method->getServiceName()
            && $this->api->isSelfConfigured()
            && !\XLite\Core\Config::getInstance()->Security->customer_security
        ) {
            return 'https';
        }

        return '';
    }

    /**
     * Get allowed currencies
     * https://www.paypal.com/us/smarthelp/article/what-currencies-does-paypal-for-marketplaces-support-ts2125
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return array
     */
    protected function getAllowedCurrencies(\XLite\Model\Payment\Method $method)
    {
        return [
            'AUD', 'BRL', 'GBP', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'TWD', 'NZD',
            'NOK', 'PHP', 'PLN', 'RUB', 'SGD', 'SEK', 'CHF', 'THB', 'TRY', 'USD',
        ];
    }
}
