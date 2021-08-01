<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\Model\Payment\Processor;

/**
 * Braintree payments processor
 */
class BraintreeVZ extends \XLite\Model\Payment\Base\CreditCard
{
    /**
     * Get operation types
     *
     * @return array
     */
    public function getOperationTypes()
    {
        return array(
            self::OPERATION_SALE,
            self::OPERATION_AUTH,
            self::OPERATION_CAPTURE,
            self::OPERATION_CAPTURE_PART,
            self::OPERATION_VOID,
            self::OPERATION_REFUND,
            self::OPERATION_REFUND_PART,
        );
    }

    /**
     * Get settings widget or template
     *
     * @return string Widget class name or template path
     */
    public function getSettingsWidget()
    {
        return '\XLite\Module\QSL\BraintreeVZ\View\Tabs\Config';
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
        return \XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::getInstance()->isConfigured();
    }

    /**
     * Check - payment processor is applicable for specified order or not
     *
     * @param \XLite\Model\Order          $order  Order
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isApplicable(\XLite\Model\Order $order, \XLite\Model\Payment\Method $method)
    {
        return parent::isApplicable($order, $method)
            && $this->isConfigured($method);
    }

    /**
     * Get return type
     *
     * @return string
     */
    public function getReturnType()
    {
        return self::RETURN_TYPE_HTTP_REDIRECT;
    }

    /**
     * Returns the list of settings available for this payment processor
     *
     * @return array
     */
    public function getAvailableSettings()
    {
        return array(
            'merchantId',
            'accessToken',
            'prefix',
        );
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
     * Check - payment method has enabled test mode or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isTestMode(\XLite\Model\Payment\Method $method)
    {
        return (bool)$method->getSetting('testMode');
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
        $result = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE;

        if (
            $method
            && !$method->getSetting('isAutoSettle')
        ) {
            $result = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH;
        }

        return $result;
    }

    /**
     * Get allowed backend transactions
     *
     * @return array
     */
    public function getAllowedTransactions()
    {
        return [
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_VOID,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_PART,
            \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_REFUND_MULTI,
        ];
    }

    /**
     * Get input template
     *
     * @return string
     */
    public function getInputTemplate()
    {
        return 'modules/QSL/BraintreeVZ/checkout/body.twig';
    }

    /**
     * Do 'CAPTURE' request on Authorized transaction.
     * Returns true on success or false on failure
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Trandaction
     *
     * @return boolean
     */
    protected function doCapture(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $result = \XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::getInstance()
            ->processCapture(
                $transaction->getPaymentTransaction()
            ); 

        if ($result) {

            $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);
            $transaction->getPaymentTransaction()->getOrder()->setPaymentStatus(
                \XLite\Model\Order\Status\Payment::STATUS_PAID
            );

            \XLite\Core\TopMessage::getInstance()->addInfo('Payment has been captured successfully');

        } else {

            $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_FAILED);
            
            // For unsuccessful operation the top message is being set
            // in the Core\Braintree by the response from Braintree
        }

        $transaction->update();

        return $result;
    }

    /**
     * Do 'VOID' request.
     * Returns true on success or false on failure
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doVoid(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $result = \XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::getInstance()
            ->processVoid(
                $transaction->getPaymentTransaction()
            );

        if ($result) {

            $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);
            $transaction->getPaymentTransaction()->getOrder()->setPaymentStatus(
                \XLite\Model\Order\Status\Payment::STATUS_DECLINED
            );

            \XLite\Core\TopMessage::getInstance()->addInfo('Payment has been voided successfully');

        } else {

            $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_FAILED);

            // For unsuccessful operation the top message is being set
            // in the Core\Braintree by the response from Braintree
        }

        $transaction->update();

        return $result;

    }

    /**
     * Do 'CREDIT' request.
     * Returns true on success or false on failure
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return boolean
     */
    protected function doRefund(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $result = \XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::getInstance()
            ->processRefund(
                $transaction->getPaymentTransaction(),
                $transaction->getValue()
            );

        if ($result) {

            $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_SUCCESS);
            $transaction->getPaymentTransaction()->getOrder()->setPaymentStatus(
                \XLite\Model\Order\Status\Payment::STATUS_REFUNDED
            );

            \XLite\Core\TopMessage::getInstance()->addInfo('Payment has been refunded successfully');

        } else {

            $transaction->setStatus(\XLite\Model\Payment\Transaction::STATUS_FAILED);

            // For unsuccessful operation the top message is being set
            // in the Core\Braintree by the response from Braintree
        }

        $transaction->update();

        return $result;
    }

    /**
     * Do partial refund.
     * Returns true on success or false on failure
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction
     *
     * @return bool
     */
    protected function doRefundPart(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        return $this->doRefund($transaction);
    }

    /**
     * Do multi refund.
     * Returns true on success or false on failure
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction
     *
     * @return bool
     */
    protected function doRefundMulti(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        return $this->doRefund($transaction);
    }

    /**
     * Do initial payment
     *
     * @return string Status code
     */
    protected function doInitialpayment()
    {
        $result = static::FAILED;

        $braintreeResult = \XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::getInstance()->processCheckout($this->transaction);

        if ($braintreeResult) {
            $result = static::COMPLETED;
        }

        return $result;
    }

}
