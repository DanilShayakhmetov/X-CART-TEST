<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Payment\Base;

use XLite\Core\Request;

/**
 * Abstract online (gateway-based) processor
 */
abstract class Online extends \XLite\Model\Payment\Base\Processor
{
    /**
     * Default return transaction id field name
     */
    const RETURN_TXN_ID = 'txnId';


    /**
     * Return response type
     */
    const RETURN_TYPE_HTTP_REDIRECT = 'http';
    const RETURN_TYPE_HTML_REDIRECT = 'html';
    const RETURN_TYPE_HTML_REDIRECT_WITH_IFRAME_DESTROYING = 'html_iframe';
    const RETURN_TYPE_CUSTOM        = 'custom';


    /**
     * Process return
     *
     * @param \XLite\Model\Payment\Transaction $transaction Return-owner transaction
     */
    public function processReturn(\XLite\Model\Payment\Transaction $transaction)
    {
        $this->transaction = $transaction;

        if ($this->transaction->getOrder()) {
            $this->transaction->getOrder()->renewSoft();
        }

        $this->logReturn(\XLite\Core\Request::getInstance()->getData());
    }

    /**
     * Get return type
     *
     * @return string
     */
    public function getReturnType()
    {
        return static::RETURN_TYPE_HTTP_REDIRECT;
    }

    /**
     * Process callback
     *
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     */
    public function processCallback(\XLite\Model\Payment\Transaction $transaction)
    {
        $this->transaction = $transaction;

        $this->logCallback(\XLite\Core\Request::getInstance()->getPostDataWithArrayValues());
    }

    /**
     * Process callback not ready
     *
     * @param \XLite\Model\Payment\Transaction $transaction Callback-owner transaction
     */
    public function processCallbackNotReady(\XLite\Model\Payment\Transaction $transaction)
    {
    }

    /**
     * Get callback request owner transaction or null
     *
     * @return \XLite\Model\Payment\Transaction
     */
    public function getCallbackOwnerTransaction()
    {
        return null;
    }

    /**
     * Mark callback request as invalid
     *
     * @param string $message Message
     */
    public function markCallbackRequestAsInvalid($message)
    {
        \XLite\Logger::getInstance()->log(
            'Callback request is invalid: ' . $message . PHP_EOL
            . 'Payment gateway: ' . $this->transaction->getPaymentMethod()->getServiceName() . PHP_EOL
            . 'order #' . $this->transaction->getOrder()->getOrderId()
            . ' / transaction #' . $this->transaction->getTransactionId() . PHP_EOL,
            LOG_WARNING
        );
    }


    /**
     * Get client IP (v4 or v6)
     *
     * @param bool $ipv6 Pass true to allow IPv6 addresses.
     * @return string
     */
    protected function getClientIP($ipv6 = false)
    {
        $result = Request::getInstance()->getClientIp();

        if (!$ipv6) {
            $ipv4 = '/^((25[0-5]|2[0-4][\d]|[01]?[\d][\d]?)\.){3}(25[0-5]|2[0-4][\d]|[01]?[\d][\d]?)$/';
            if (preg_match($ipv4, $result)) {
                return $result;
            }

            return null;
        }

        return $result;
    }

    /**
     * Get invoice description
     *
     * @return string
     */
    protected function getInvoiceDescription()
    {
        return 'Payment transaction: ' . $this->getTransactionId();
    }

    /**
     * Define saved into transaction data schema
     *
     * @return array
     */
    protected function defineSavedData()
    {
        return array();
    }

    /**
     * Save request data into transaction
     */
    protected function saveDataFromRequest($backendTransaction = null)
    {
        $this->saveFilteredData(\XLite\Core\Request::getInstance()->getData(), $backendTransaction);
    }

    /**
     * Filter input array $data by keys and save in the transaction data
     *
     * @param array                                   $data               Array of data to save
     * @param \XLite\Model\Payment\BackendTransaction $backendTransaction Backend transaction object OPTIONAL
     */
    protected function saveFilteredData($data, $backendTransaction = null)
    {
        foreach ($this->defineSavedData() as $key => $name) {
            if (isset($data[$key])) {
                $this->setDetail($key, $data[$key], $name, $backendTransaction);
            }
        }
    }

    /**
     * Array cell mask
     *
     * @param array  $list Array
     * @param string $name CEll key
     *
     * @return array
     */
    protected function maskCell(array $list, $name)
    {
        if (isset($list[$name])) {
            $list[$name] = str_repeat('*', strlen($list[$name]));
        }

        return $list;
    }

    /**
     * Log return request
     *
     * @param array $list Request data
     */
    protected function logReturn(array $list)
    {
        \XLite\Logger::getInstance()->log(
            $this->transaction->getPaymentMethod()->getServiceName() . ' payment gateway : return' . PHP_EOL
            . 'Data: ' . var_export($list, true),
            LOG_DEBUG
        );
    }

    /**
     * Log callback
     *
     * @param array $list Callback data
     */
    protected function logCallback(array $list)
    {
        \XLite\Logger::getInstance()->log(
            $this->transaction->getPaymentMethod()->getServiceName() . ' payment gateway : callback' . PHP_EOL
            . 'Data: ' . var_export($list, true),
            LOG_DEBUG
        );
    }

    /**
     * Get transactionId-based return URL
     *
     * @param string  $fieldName TransactionId field name OPTIONAL
     * @param boolean $withId    Add to URL transaction id or not OPTIONAL
     * @param boolean $asCancel  Mark URL as cancel action OPTIONAL
     *
     * @return string
     */
    protected function getReturnURL($fieldName = self::RETURN_TXN_ID, $withId = false, $asCancel = false)
    {
        $query = array(
            'txn_id_name' => $fieldName ?: static::RETURN_TXN_ID,
        );

        if ($withId) {
            $query[$query['txn_id_name']] = $this->transaction->getPublicTxnId();
        }

        if ($asCancel) {
            $query['cancel'] = 1;
        }

        return \XLite::getInstance()->getShopURL(
            \XLite\Core\Converter::buildURL('payment_return', '', $query, \XLite::getCustomerScript()),
            \XLite\Core\Config::getInstance()->Security->customer_security
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
        $query = array(
            'txn_id_name' => $fieldName ?: static::RETURN_TXN_ID,
        );

        if ($withId) {
            $query[$query['txn_id_name']] = $this->transaction->getPublicTxnId();
        }

        return \XLite::getInstance()->getShopURL(
            \XLite\Core\Converter::buildURL('callback', '', $query, \XLite::getCustomerScript()),
            \XLite\Core\Config::getInstance()->Security->customer_security
        );
    }

    /**
     * Convert string to float
     *
     * @param string $money                 Money amount
     * @param string $thousandDelimiter     Thousand delimiter
     * @param string $decimalDelimiter      Decimal delimiter
     *
     * @return float
     */
    protected function parseMoneyFromString($money, $thousandDelimiter, $decimalDelimiter)
    {
        $preparedString = str_replace(
            array($thousandDelimiter, $decimalDelimiter),
            array('', '.'),
            $money
        );

        return (float) $preparedString;
    }

    /**
     * Check total (transaction total and total from gateway response)
     *
     * @param float $total Total from gateway response
     *
     * @return boolean
     */
    protected function checkTotal($total)
    {
        $result = true;

        $currency = $this->transaction->getCurrency();
        
        if ($total && $currency->roundValue($this->transaction->getValue()) != $currency->roundValue($total)) {
            $msg = 'Total amount doesn\'t match. Transaction total: ' . $this->transaction->getValue()
                . '; payment gateway amount: ' . $total;
            $this->setDetail(
                'total_checking_error',
                $msg,
                'Hacking attempt'
            );

            $result = false;
        }

        return $result;
    }

    /**
     * Check currency (order currency and transaction response currency)
     *
     * @param string $currency Transaction response currency code
     *
     * @return boolean
     */
    protected function checkCurrency($currency)
    {
        $result = true;

        if ($currency && $this->transaction->getCurrency()->getCode() != $currency) {
            $msg = 'Currency code doesn\'t match. Order currency: '
                . $this->transaction->getCurrency()->getCode()
                . '; payment gateway currency: ' . $currency;
            $this->setDetail(
                'currency_checking_error',
                $msg,
                'Hacking attempt details'
            );

            $result = false;
        }

        return $result;
    }


}
