<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Model\Payment\Processor;

/**
 * Paypal Express Checkout payment processor
 */
class ExpressCheckoutMerchantAPI extends \XLite\Module\CDev\Paypal\Model\Payment\Processor\ExpressCheckout
{
    /**
     * API Instance
     *
     * @var \XLite\Module\CDev\Paypal\Core\PaypalAPI
     */
    protected $api = null;

    // {{{ Common

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->api = new \XLite\Module\CDev\Paypal\Core\PaypalAPI();
    }

    /**
     * Get allowed backend transactions
     *
     * @return string[] Status code
     */
    public function getAllowedTransactions()
    {
        $method = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(\XLite\Module\CDev\Paypal\Main::PP_METHOD_EC);

        return $method && $this->api->isConfiguredApiSolution()
            ? parent::getAllowedTransactions()
            : [];
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
     * Check - payment method is configured or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        $paypalCommercePlatform = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(
            \XLite\Module\CDev\Paypal\Main::PP_METHOD_PCP
        );

        return \XLite\Model\Payment\Base\Processor::isConfigured($method)
            && (!$this->getAllowedMerchantCountries()
                || in_array(\XLite\Core\Config::getInstance()->Company->location_country, $this->getAllowedMerchantCountries(), true))
            && $this->api->isConfigured()
            && (!$paypalCommercePlatform || !$paypalCommercePlatform->isEnabled());
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
        if (parent::isConfigured($method)) {
            $paypalCommercePlatform = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(
                \XLite\Module\CDev\Paypal\Main::PP_METHOD_PCP
            );

            if ($paypalCommercePlatform->isEnabled()) {
                return static::t('PayPal checkout and PayPal express checkout (legacy) / PayPal Payments Advanced are not able to work together.');
            }
        }

        return parent::getWarningNote($method);
    }

    // }}}

    // {{{ Set express checkout


    /**
     * Perform 'SetExpressCheckout' request and get Token value from Paypal
     *
     * @param \XLite\Model\Payment\Method           $method Payment method
     * @param \XLite\Model\Payment\Transaction|null $transaction
     *
     * @return string
     * @see    https://developer.paypal.com/docs/classic/api/merchant/SetExpressCheckout_API_Operation_NVP/
     */
    public function doSetExpressCheckout(\XLite\Model\Payment\Method $method, \XLite\Model\Payment\Transaction $transaction = null)
    {
        $token = null;

        $this->transaction = $transaction;
        $responseData = $this->doRequest('SetExpressCheckout');

        if (!empty($responseData['TOKEN'])) {
            $token = $responseData['TOKEN'];

        } else {
            $this->setDetail(
                'status',
                isset($responseData['L_LONGMESSAGE0']) ? $responseData['L_LONGMESSAGE0'] : 'Unknown',
                'Status'
            );

            $transaction = \XLite\Model\Cart::getInstance()->getFirstOpenPaymentTransaction();
            if ($transaction) {
                $this->processFailTryPayment($transaction);
            }

            $this->errorMessage = isset($responseData['L_LONGMESSAGE0']) ? $responseData['L_LONGMESSAGE0'] : null;
        }

        return $token;
    }

    /**
     * Get array of parameters for SET_EXPRESS_CHECKOUT request
     *
     * @return array
     */
    protected function getSetExpressCheckoutRequestParams()
    {
        $params = $this->api->convertSetExpressCheckoutParams($this->getOrder());

        $orderNumber = $this->getTransactionId($this->getSetting('prefix'));
        $params['PAYMENTREQUEST_0_INVNUM'] = $orderNumber;
        $params['PAYMENTREQUEST_0_CUSTOM'] = $orderNumber;

        return $params;

    }

    // }}}

    // {{{ getExpressCheckoutDetails

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

        if (!isset($this->transaction)) {
            $this->transaction = new \XLite\Model\Payment\Transaction();
            $this->transaction->setPaymentMethod($method);
        }

        $responseData = $this->doRequest('GetExpressCheckoutDetails');

        if (!empty($responseData) && isset($responseData['ACK']) && $this->isSuccessACK($responseData['ACK'])) {
            $data = $responseData;
        }

        return $data;
    }

    /**
     * Get list of ACK values for successfull transaction
     *
     * @return array
     */
    protected static function getSuccessACKValues()
    {
        return ['Success', 'SuccessWithWarning'];
    }

    /**
     * Return true if ACK value is successful
     *
     * @param string $value ACK value
     *
     * @return boolean
     */
    protected function isSuccessACK($value)
    {
        return in_array($value, static::getSuccessACKValues());
    }

    /**
     * Return array of parameters for 'GetExpressCheckoutDetails' request
     *
     * @return array
     */
    protected function getGetExpressCheckoutDetailsRequestParams()
    {
        $token = \XLite\Core\Session::getInstance()->ec_token;

        return $this->api->convertGetExpressCheckoutDetailsParams($token);
    }

    // }}}

    // {{{ doExpressCheckoutPayment

    /**
     * Perform 'DoExpressCheckoutPayment' request and return status of payment transaction
     *
     * @return string
     */
    protected function doDoExpressCheckoutPayment()
    {
        $status = self::FAILED;

        $transaction = $this->transaction;

        $responseData = $this->doRequest(
            'DoExpressCheckoutPayment',
            $transaction->getInitialBackendTransaction()
        );

        $transactionStatus = $transaction::STATUS_FAILED;

        if (!empty($responseData)) {
            if ($this->isSuccessACK($responseData['ACK'])) {
                if ($this->isSuccessResponse($responseData)) {
                    $transactionStatus = $transaction::STATUS_SUCCESS;
                    $status = self::COMPLETED;

                } else {
                    $transactionStatus = $transaction::STATUS_PENDING;
                    $status = self::PENDING;
                }

            } elseif ($status = $this->tryHandleExpressCheckoutError($responseData)) {
                // WARNING: no fault here, assignment is intended.
                return $status;

            } else {
                $this->setDetail(
                    'status',
                    'Failed: ' . $responseData['L_LONGMESSAGE0'],
                    'Status'
                );

                $transaction->setNote($this->getPaypalFailureNote($responseData));
            }

            // Save payment transaction data
            $this->saveFilteredData($responseData);

        } else {
            $this->setDetail(
                'status',
                'Failed: unexpected response received from PayPal',
                'Status'
            );

            $transaction->setNote('Unexpected response received from PayPal');
        }

        $transaction->setStatus($transactionStatus);

        $this->updateInitialBackendTransaction($transaction, $transactionStatus);

        \XLite\Core\Session::getInstance()->ec_token = null;
        \XLite\Core\Session::getInstance()->ec_date = null;
        \XLite\Core\Session::getInstance()->ec_payer_id = null;
        \XLite\Core\Session::getInstance()->ec_type = null;

        return $status;
    }

    /**
     * Returns human-readable Paypal error note.
     *
     * @param $responseData
     * @return string
     */
    protected function getPaypalFailureNote($responseData)
    {
        $note = $responseData['L_SHORTMESSAGE0'];

        if ($responseData['L_ERRORCODE0'] === '10417') {
            $note = 'The credit card failed bank authorization. Retry the transaction using an alternative payment method from your PayPal wallet or contact PayPal Customer Service';
        }

        if ($responseData['L_ERRORCODE0'] === '10485') {
            $note = 'Payment has not been authorized by the user. Try to place the order again or contact PayPal Customer Service';
        }

        return $note;
    }

    /**
     * Return array of parameters for 'DoExpressCheckoutPayment' request
     *
     * @return array
     */
    protected function getDoExpressCheckoutPaymentRequestParams()
    {
        $transaction = $this->transaction;
        $token = \XLite\Core\Session::getInstance()->ec_token;
        $payerId = \XLite\Core\Session::getInstance()->ec_payer_id;

        $params = $this->api->convertDoExpressCheckoutPaymentParams($transaction, $token, $payerId);

        $orderNumber = $this->getTransactionId($this->getSetting('prefix'));
        $params['PAYMENTREQUEST_0_INVNUM'] = $orderNumber;
        $params['PAYMENTREQUEST_0_CUSTOM'] = $orderNumber;

        return $params;

    }

    /**
     * Return true if Paypal response is a success transaction response
     *
     * @param array $response Response data
     *
     * @return boolean
     */
    protected function isSuccessResponse($response)
    {
        $result = in_array(strtolower($response['PAYMENTINFO_0_PENDINGREASON']), ['none', 'completed']);

        if (!$result) {
            $result = (
                'authorization' == $response['PAYMENTINFO_0_PENDINGREASON']
                && \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH == $this->transaction->getType()
            );
        }

        return $result;
    }

    // }}}

    /**
     * Get list of IPN locking request types
     *
     * @return array
     */
    protected function getIpnLockingRequests()
    {
        return [
            'DoVoid',
            'DoCapture',
            'RefundTransaction',
        ];
    }

    // {{{ doVoid

    /**
     * doVoid
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return boolean
     * @see    https://developer.paypal.com/webapps/developer/docs/classic/api/merchant/DoVoid_API_Operation_NVP/
     */
    public function doVoid(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $processor = $this;

        return $this->processRequest('DoVoid', $transaction, 
            function ($responseData, $state) use ($transaction, $processor) {
                if ($processor->isSuccessACK($responseData['ACK'])) {
                    $state['result'] = true;
                    $state['status'] = \XLite\Model\Payment\Transaction::STATUS_SUCCESS;

                    $transaction->getPaymentTransaction()->getOrder()->setPaymentStatus(
                        \XLite\Model\Order\Status\Payment::STATUS_DECLINED
                    );

                    // save transaction id for IPN
                    $transaction->setDataCell(
                        'PPREF',
                        $responseData['AUTHORIZATIONID'],
                        'Unique PayPal transaction ID (AUTHORIZATIONID)'
                    );

                    \XLite\Core\TopMessage::getInstance()->addInfo('Payment has been voided successfully');

                } else {
                    \XLite\Core\TopMessage::getInstance()
                        ->addError('Transaction failure. PayPal response: ' . $responseData['L_LONGMESSAGE0']);
                }

                return $state;
            }
        );
    }

    /**
     * Return array of parameters for 'DoVoid' request
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return array
     * @see    https://developer.paypal.com/webapps/developer/docs/classic/api/merchant/DoVoid_API_Operation_NVP/
     */
    protected function getDoVoidRequestParams(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $authorizationId = $this->getTransactionReferenceId($transaction);

        return $this->api->convertDoVoidParams($authorizationId);
    }

    // }}}

    // {{{ doCapture

    /**
     * doCapture
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return boolean
     * @see    https://developer.paypal.com/webapps/developer/docs/classic/api/merchant/DoCapture_API_Operation_NVP/
     */
    public function doCapture(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $processor = $this;

        return $this->processRequest('DoCapture', $transaction, 
            function ($responseData, $state) use ($transaction, $processor) {
                if ($processor->isSuccessACK($responseData['ACK'])) {
                    $state['result'] = true;
                    $state['status'] = \XLite\Model\Payment\Transaction::STATUS_SUCCESS;

                    $transaction->getPaymentTransaction()->getOrder()->setPaymentStatus(
                        \XLite\Model\Order\Status\Payment::STATUS_PAID
                    );

                    \XLite\Core\TopMessage::getInstance()->addInfo('Payment has been captured successfully');

                    $transaction->setDataCell(
                        $processor->getReferenceIdField(),
                        $responseData['TRANSACTIONID'],
                        'Transaction ID'
                    );

                    // save transaction id for IPN
                    $transaction->setDataCell(
                        'PPREF',
                        $responseData['TRANSACTIONID'],
                        'Unique PayPal transaction ID (TRANSACTIONID)'
                    );

                } else {
                    \XLite\Core\TopMessage::getInstance()
                        ->addError('Transaction failure. PayPal response: ' . $responseData['L_LONGMESSAGE0']);
                }

                return $state;
            }
        );
    }

    /**
     * Return array of parameters for 'DoCapture' request
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return array
     * @see    https://developer.paypal.com/webapps/developer/docs/classic/api/merchant/DoCapture_API_Operation_NVP/
     */
    protected function getDoCaptureRequestParams(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $authorizationId = $this->getTransactionReferenceId($transaction);

        return $this->api->convertDoCaptureParams($transaction, $authorizationId);
    }

    // }}}

    // {{{ refundTransaction

    /**
     * doRefund
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return boolean
     * @see    https://developer.paypal.com/webapps/developer/docs/classic/api/merchant/RefundTransaction_API_Operation_NVP/
     */
    public function doRefund(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $processor = $this;

        return $this->processRequest('RefundTransaction', $transaction, 
            function ($responseData, $state) use ($transaction, $processor) {
                if ($processor->isSuccessACK($responseData['ACK'])) {
                    $state['result'] = true;
                    $state['status'] = \XLite\Model\Payment\Transaction::STATUS_SUCCESS;

                    // save transaction id for IPN
                    $transaction->setDataCell(
                        'PPREF',
                        $responseData['REFUNDTRANSACTIONID'],
                        'Unique PayPal transaction ID (REFUNDTRANSACTIONID)'
                    );

                    \XLite\Core\TopMessage::getInstance()->addInfo('Payment has bes refunded successfully');

                } else {
                    \XLite\Core\TopMessage::getInstance()
                        ->addError('Transaction failure. PayPal response: ' . $responseData['L_LONGMESSAGE0']);
                }

                return $state;
            }
        );
    }

    /**
     * Return array of parameters for 'RefundTransaction' request
     *
     * @param \XLite\Model\Payment\BackendTransaction $transaction Transaction
     *
     * @return array
     * @see    https://developer.paypal.com/webapps/developer/docs/classic/api/merchant/RefundTransaction_API_Operation_NVP/
     */
    protected function getRefundTransactionRequestParams(\XLite\Model\Payment\BackendTransaction $transaction)
    {
        $transactionId = $this->getTransactionReferenceId($transaction);

        return $this->api->convertRefundTransactionParams($transaction, $transactionId);
    }

    // }}}

    // {{{ Additional methods

    /**
     * Translate array of data received from Paypal to the array for updating cart
     *
     * @param array $paypalData Array of customer data received from Paypal
     *
     * @return array
     */
    public function prepareBuyerData($paypalData)
    {
        $countryCode = \Includes\Utils\ArrayManager::getIndex($paypalData, 'SHIPTOCOUNTRYCODE');
        $country = \XLite\Core\Database::getRepo('XLite\Model\Country')
            ->findOneByCode($countryCode);

        $stateCode = \Includes\Utils\ArrayManager::getIndex($paypalData, 'SHIPTOSTATE');
        $state = ($country && $stateCode)
            ? \XLite\Core\Database::getRepo('XLite\Model\State')
                ->findOneByCountryAndState($country->getCode(), mb_strtoupper($stateCode))
            : null;

        $street = trim(
            \Includes\Utils\ArrayManager::getIndex($paypalData, 'SHIPTOSTREET')
            . ' '
            . \Includes\Utils\ArrayManager::getIndex($paypalData, 'SHIPTOSTREET2')
        );

        $data = [
            'shippingAddress' => [
                'name' => (string) \Includes\Utils\ArrayManager::getIndex($paypalData, 'SHIPTONAME'),
                'street' => $street,
                'country_code' => $countryCode,
                'country' => $country ?: '',
                'state_id' => $state ? $state->getStateId() : null,
                'state' => $state ?: (string) $stateCode,
                'custom_state' => $state ? $state->getState() : (string) $stateCode,
                'city' => (string) \Includes\Utils\ArrayManager::getIndex($paypalData, 'SHIPTOCITY'),
                'zipcode' => (string) \Includes\Utils\ArrayManager::getIndex($paypalData, 'SHIPTOZIP'),
                'phone' => (string) \Includes\Utils\ArrayManager::getIndex($paypalData, 'PHONENUM'),
            ],
        ];

        return $data;
    }

    /**
     * Get allowed currencies
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return array
     * @see    https://developer.paypal.com/docs/classic/api/currency_codes/
     */
    protected function getAllowedCurrencies(\XLite\Model\Payment\Method $method)
    {
        return array_merge(
            parent::getAllowedCurrencies($method),
            [
                'AUD', 'BRL', 'CAD', 'CZK', 'DKK',
                'EUR', 'HKD', 'HUF', 'ILS', 'JPY',
                'MYR', 'MXN', 'NOK', 'NZD', 'PHP',
                'PLN', 'GBP', 'RUB', 'SGD', 'SEK',
                'CHF', 'TWD', 'THB', 'TRY', 'USD',
            ]
        );
    }

    // }}}

    // {{{ Reference id

    /**
     * Get reference ID field name for backend transactions
     *
     * @return string
     */
    protected function getReferenceIdField()
    {
        return 'PAYMENTINFO_0_TRANSACTIONID';
    }

    // }}}
}
