<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\SagePay\Model\Payment\Processor;

/**
 * SagePay form protocol processor
 *
 * Find the latest API document here:
 * http://www.sagepay.co.uk/file/6941/download-document/FORM_Protocol_and_Integration_Guidelines_300114.pdf
 */
class SagePayForm extends \XLite\Model\Payment\Base\WebBased
{
    const THOUSAND_DELIMITER = ',';
    const DECIMAL_DELIMITER  = '.';

    /**
     * Get operation types
     *
     * @return array
     */
    public function getOperationTypes()
    {
        return [
            self::OPERATION_SALE,
            self::OPERATION_AUTH,
        ];
    }

    /**
     * Get settings widget or template
     *
     * @return string Widget class name or template path
     */
    public function getSettingsWidget()
    {
        return 'modules/XC/SagePay/config.twig';
    }

    /**
     * Process return
     *
     * @param \XLite\Model\Payment\Transaction $transaction Return-owner transaction
     *
     * @return void
     */
    public function processReturn(\XLite\Model\Payment\Transaction $transaction)
    {
        parent::processReturn($transaction);

        $request     = \XLite\Core\Request::getInstance();
        $requestBody = $this->decode($request->crypt);

        $status = $transaction::STATUS_FAILED;

        \XLite\Module\XC\SagePay\Main::addLog(
            'processReturnRawResult',
            $request->getData()
        );

        \XLite\Module\XC\SagePay\Main::addLog(
            'processReturn',
            $requestBody
        );

        if (isset($requestBody['Status'])) {

            if ('OK' === $requestBody['Status']) {
                // Success status
                $this->setDetail('TxAuthNo', $requestBody['TxAuthNo'], 'Authorisation code of the transaction');
                $status = $transaction::STATUS_SUCCESS;
            } else {
                // Some error occuried
                $status = $transaction::STATUS_FAILED;
            }

            $this->setDetail('StatusDetail', $requestBody['StatusDetail'], 'Status details');
            $this->transaction->setNote($requestBody['StatusDetail']);
        } else {
            // Invalid response
            $this->setDetail('StatusDetail', 'Invalid response was received', 'Status details');
        }

        if (isset($requestBody['VPSTxId'])) {
            $this->setDetail('VPSTxId', $requestBody['VPSTxId'], 'The unique Opayo ID of the transaction');
        }

        if (isset($requestBody['AVSCV2'])) {
            $this->setDetail('AVSCV2', $requestBody['AVSCV2'], 'AVSCV2 Status');
        }

        if (isset($requestBody['AddressResult'])) {
            $this->setDetail('AddressResult', $requestBody['AddressResult'], 'Cardholder address checking status');
        }

        if (isset($requestBody['PostCodeResult'])) {
            $this->setDetail('PostCodeResult', $requestBody['PostCodeResult'], 'Cardholder postcode checking status');
        }

        if (isset($requestBody['CV2Result'])) {
            $this->setDetail('CV2Result', $requestBody['CV2Result'], 'CV2 code checking result');
        }

        if (isset($requestBody['3DSecureStatus'])) {
            $this->setDetail('3DSecureStatus', $requestBody['3DSecureStatus'], '3DSecure checking status');
        }

        $total = $this->getSagePayTotal($requestBody);

        if (!$this->checkTotal($total)) {
            $this->setDetail('StatusDetail', 'Invalid amount value was received', 'Status details');
            $status = $transaction::STATUS_FAILED;
        }

        $this->transaction->setStatus($status);
    }

    /**
     * Returns calculated sage pay total to compare with order data. Excepts SagePay Surcharge.
     *
     * @param $requestData
     * @return float
     */
    protected function getSagePayTotal($requestData)
    {
        $total = $requestData['Amount'];

        if (!is_float($total)) {
            $total = $this->parseMoneyFromString(
                $total,
                static::THOUSAND_DELIMITER,
                static::DECIMAL_DELIMITER
            );
        }

        $surcharge = isset($requestData['Surcharge']) ? $requestData['Surcharge'] : 0.0;

        if ($surcharge && !is_float($surcharge)) {
            $surcharge = $this->parseMoneyFromString(
                $surcharge,
                static::THOUSAND_DELIMITER,
                static::DECIMAL_DELIMITER
            );
        }

        return $total - $surcharge;
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
        return self::OPERATION_AUTH == ($method ? $method->getSetting('type') : $this->getSetting('type'))
            ? \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH
            : \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE;
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
        return parent::isConfigured($method)
            && $method->getSetting('vendorName')
            && $method->getSetting('password');
    }

    /**
     * Get password help link
     *
     * @return string
     */
    public function getHelpPasswordLink()
    {
        return 'http://www.opayo.co.uk/support/12/36/encryption-password';
    }

    /**
     * Get return type
     *
     * @return string
     */
    public function getReturnType()
    {
        return self::RETURN_TYPE_HTML_REDIRECT;
    }

    /**
     * Returns the list of settings available for this payment processor
     *
     * @return array
     */
    public function getAvailableSettings()
    {
        return [
            'vendorName',
            'password',
            'test',
            'type',
            'prefix',
        ];
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
        return (bool) $method->getSetting('test');
    }

    /**
     * Get redirect form URL
     *
     * @return string
     */
    protected function getFormURL()
    {
        return $this->getSetting('test')
            ? 'https://test.sagepay.com/gateway/service/vspform-register.vsp'
            : 'https://live.sagepay.com/gateway/service/vspform-register.vsp';
    }

    /**
     * Get redirect form fields list
     *
     * @return array
     */
    protected function getFormFields()
    {
        return [
            'VPSProtocol' => '3.00',
            'TxType'      => $this->getSetting('type') === self::OPERATION_SALE
                ? 'PAYMENT'
                : 'DEFERRED',
            'Vendor'      => $this->getSetting('vendorName'),
            'Crypt'       => $this->getCrypt(),
        ];
    }

    /**
     * Returns the crypted ordering information
     *
     * @return string
     */
    protected function getCrypt()
    {
        $fields = $this->getOrderingInformation();

        $cryptedFields = [];
        foreach ($fields as $key => $value) {
            $cryptedFields[] = $key . '=' . $value;
        }

        return $this->encryptAndEncode(implode('&', $cryptedFields));
    }

    /**
     * Returns the array of fields with ordering information
     *
     * @return array
     */
    protected function getOrderingInformation()
    {
        $currency = $this->transaction->getCurrency();

        $shippingAddress = $this->getProfile()->getShippingAddress();
        if (null === $shippingAddress) {
            $shippingAddress = $this->getProfile()->getBillingAddress();
        }

        $fields = [
            'VendorTxCode' => $this->getTransactionId(),
            'ReferrerID'   => '653E8C42-AD93-4654-BB91-C645678FA97B',
            'Amount'       => round($this->transaction->getValue(), 2),
            'Currency'     => strtoupper($currency->getCode()),
            'Description'  => 'Your Cart',

            'SuccessURL' => $this->getReturnURL(null, true),
            'FailureURL' => $this->getReturnURL(null, true, true),

            'CustomerName'  => $this->getProfile()->getBillingAddress()->getFirstname()
                . ' '
                . $this->getProfile()->getBillingAddress()->getLastname(),
            'CustomerEMail' => $this->getProfile()->getLogin(),
            'VendorEMail'   => \XLite\Core\Mailer::getOrdersDepartmentMail(),
            'SendEMail'     => $this->getOptionValueSendEMail(),

            'BillingSurname'    => $this->getProfile()->getBillingAddress()->getLastname(),
            'BillingFirstnames' => $this->getProfile()->getBillingAddress()->getFirstname(),
            'BillingAddress1'   => $this->getProfile()->getBillingAddress()->getStreet(),
            'BillingCity'       => $this->getProfile()->getBillingAddress()->getCity(),
            'BillingPostCode'   => $this->getProfile()->getBillingAddress()->getZipcode(),
            'BillingCountry'    => strtoupper($this->getProfile()->getBillingAddress()->getCountry()->getCode()),

            'DeliverySurname'    => $shippingAddress->getLastname(),
            'DeliveryFirstnames' => $shippingAddress->getFirstname(),
            'DeliveryAddress1'   => $shippingAddress->getStreet(),
            'DeliveryCity'       => $shippingAddress->getCity(),
            'DeliveryPostCode'   => $shippingAddress->getZipcode(),
            'DeliveryCountry'    => strtoupper($shippingAddress->getCountry()->getCode()),

            'Basket'        => $this->getBasket(),
            'AllowGiftAid'  => 0,
            'ApplyAVSCV2'   => 0,
            'Apply3DSecure' => 0,
        ];

        if ('US' === $fields['BillingCountry']) {
            $fields['BillingState'] = $this->getProfile()->getBillingAddress()->getState()->getCode();
        }

        if ('US' === $fields['DeliveryCountry']) {
            $fields['DeliveryState'] = $shippingAddress->getState()->getCode();
        }

        $this->cropFieldsValues($fields);

        return $fields;
    }

    /**
     * Send confirmation emails setting. Returns '1' if this option is enabled.
     *
     * @return string
     */
    protected function getOptionValueSendEMail()
    {
        return '1';
    }

    /**
     * Returns the basket information
     *
     * @return string
     */
    protected function getBasket()
    {
        return '';
    }

    /**
     * Decode the crypted response text
     *
     * @param string $strIn Crypted response text
     *
     * @return array
     */
    protected function decode($strIn)
    {
        $sagePayResponse = [];
        $decodedString   = $this->decodeAndDecrypt($strIn);
        parse_str($decodedString, $sagePayResponse);

        return $sagePayResponse;
    }

    /**
     * Encryption of the text
     *
     * @param string $strIn Text for encryption
     *
     * @return string
     */
    protected function encryptAndEncode($strIn)
    {
        $aes = new \phpseclib\Crypt\AES();

        $aes->setKey($this->getSetting('password'));
        $aes->setIV($this->getSetting('password'));
        $aes->setKeyLength(128);

        return '@' . bin2hex(
            $aes->encrypt($strIn)
        );
    }

    /**
     * Decode the text
     *
     * @param string $strIn Text to decode
     *
     * @return string
     */
    protected function decodeAndDecrypt($strIn)
    {
        $aes = new \phpseclib\Crypt\AES();

        $aes->setKey($this->getSetting('password'));
        $aes->setIV($this->getSetting('password'));
        $aes->setKeyLength(128);

        return $aes->decrypt(pack('H*', substr($strIn, 1)));
    }

    /**
     * Padding of the text with the provided block sizing
     *
     * @param string  $text      Text
     * @param integer $blocksize Block size
     *
     * @return string
     */
    protected function pkcs5Pad($text, $blocksize)
    {
        $pad = $blocksize - (strlen($text) % $blocksize);

        return $text . str_repeat(chr($pad), $pad);
    }

    protected function cropFieldsValues(&$fields) {
        $lengths2fields = [
            'VendorTxCode' => 40,
            'VendorEMail' => 255,
            'CustomerName' => 100,
            'CustomerEMail' => 255,
            'FailureURL' => 2000,
            'SuccessURL' => 2000,
            'BillingSurname' => 20,
            'BillingFirstnames' => 20,
            'BillingAddress1' => 100,
            'BillingCity' => 40,
            'DeliverySurname' => 20,
            'DeliveryFirstnames' => 20,
            'DeliveryAddress1' => 100,
            'DeliveryCity' => 40,
            'BillingPostCode' => 10,
        ];

        foreach ($lengths2fields as $fieldName => $length) {
            if (!empty($fields[$fieldName]) && strlen($fields[$fieldName]) > $length) {
                $fields[$fieldName] = substr($fields[$fieldName],0, $length);
            }
        }
    }
}
