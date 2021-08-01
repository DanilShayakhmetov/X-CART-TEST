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
class ExpressCheckout extends \XLite\Module\CDev\Paypal\Model\Payment\Processor\APaypal
{
    /**
     * Express Checkout flow types definition
     */
    const EC_TYPE_SHORTCUT = 'shortcut';
    const EC_TYPE_MARK     = 'mark';

    /**
     * Express Checkout token TTL is 3 hours (10800 seconds)
     */
    const TOKEN_TTL = 10800;

    /**
     * Maximum tries to checkout when getting 10486 error
     */
    const MAX_TRIES = 3;

    /**
     * Referral page URL
     *
     * @var string
     */
    protected $referralPageURL = 'https://www.paypal.com/webapps/mpp/referral/paypal-express-checkout?partner_id=';

    /**
     * Knowledge base page URL
     *
     * @var string
     */
    protected $knowledgeBasePageURL = 'https://kb.x-cart.com/payments/paypal/setting_up_paypal_express_checkout.html';

    /**
     * Error message
     *
     * @var string
     */
    protected $errorMessage = null;

    /**
     * Tracks state of doExpressCheckoutPayment in case of recursive call
     * @var bool
     */
    protected $doExpressCheckoutPaymentRecursiveCall = false;

    // {{{ Common

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $method = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(
            \XLite\Module\CDev\Paypal\Main::PP_METHOD_EC
        );

        $this->api->setMethod($method);
    }

    /**
     * Get payment method row checkout template
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getCheckoutTemplate(\XLite\Model\Payment\Method $method)
    {
        return 'modules/CDev/Paypal/checkout/expressCheckout.twig';
    }

    /**
     * Get input template
     *
     * @return string
     */
    public function getInputTemplate()
    {
        return 'modules/CDev/Paypal/checkout/ec_checkout_box.twig';
    }

    /**
     * Get the list of merchant countries where this payment processor can work
     *
     * @return array
     */
    public function getAllowedMerchantCountries()
    {
        return ['US', 'CA', 'AU', 'NZ'];
    }

    /**
     * @param \XLite\Model\Payment\Method $method
     *
     * @return bool
     */
    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        $paypalCommercePlatform = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(
            \XLite\Module\CDev\Paypal\Main::PP_METHOD_PCP
        );

        return parent::isConfigured($method)
            && !$paypalCommercePlatform->isEnabled();
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

    /**
     * @param \XLite\Model\Payment\Method $method
     *
     * @return string
     */
    public function getNotSwitchableReasonType(\XLite\Model\Payment\Method $method)
    {
        if (\XLite\Module\CDev\Paypal\Main::PP_METHOD_EC === $method->getServiceName()
            && !$this->isConfigured($method)) {

            $paypalCommercePlatform = \XLite\Module\CDev\Paypal\Main::getPaymentMethod(
                \XLite\Module\CDev\Paypal\Main::PP_METHOD_PCP
            );

            if ($paypalCommercePlatform->isEnabled()) {
                return 'conflict';
            }
        }

        return '';
    }

    /**
     * Returns last error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * Is In-Context Boarding SignUp available
     *
     * @return boolean
     */
    public function isInContextSignUpAvailable()
    {
        $api = \XLite\Module\CDev\Paypal\Main::getRESTAPIInstance();

        return $api->isInContextSignUpAvailable();
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
        $api = \XLite\Module\CDev\Paypal\Main::getRESTAPIInstance();
        $controller = \XLite::getController();

        if ($api->isInContextSignUpAvailable()) {
            $returnUrl = $controller->getShopURL(
                $controller->buildURL('paypal_settings', 'update_credentials')
            );
            $url = $api->getSignUpUrl($returnUrl);

        } else {
            $url = parent::getReferralPageURL($method);
        }

        return $url;
    }

    // }}}

    // {{{ URL

    /**
     * Returns payment return url
     *
     * @return string
     */
    public function getPaymentReturnUrl()
    {
        if (static::EC_TYPE_MARK == \XLite\Core\Session::getInstance()->ec_type) {
            $url = $this->getReturnURL(null, true);

        } else {
            $method = \XLite\Core\Request::getInstance()->method;

            $url = \XLite::getInstance()->getShopURL(
                \XLite\Core\Converter::buildURL(
                    'checkout',
                    'express_checkout_return',
                    [
                        'method' => $method ?: \XLite\Module\CDev\Paypal\Main::PP_METHOD_EC
                    ]
                ),
                \XLite\Core\Config::getInstance()->Security->customer_security
            );
        }

        return $url;
    }

    /**
     * Returns payment cancel url
     *
     * @return string
     */
    public function getPaymentCancelUrl()
    {
        if (static::EC_TYPE_MARK == \XLite\Core\Session::getInstance()->ec_type) {
            $url = $this->getReturnURL(null, true, true);

        } else {
            $method = \XLite\Core\Request::getInstance()->method;

            $url = \XLite::getInstance()->getShopURL(
                \XLite\Core\Converter::buildURL(
                    'checkout',
                    'express_checkout_return',
                    [
                        'method' => $method ?: \XLite\Module\CDev\Paypal\Main::PP_METHOD_EC,
                        'cancel' => 1
                    ]
                ),
                \XLite\Core\Config::getInstance()->Security->customer_security
            );
        }

        if (\XLite\Core\Request::getInstance()->cancelUrl) {
            $url .= '&cancelUrl=' . urlencode(\XLite\Core\Request::getInstance()->cancelUrl);
        }

        return $url;
    }

    // }}}

    // {{{ Payment process

    /**
     * Process return (this used when customer pay via Express Checkout mark flow)
     *
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction object
     *
     * @return void
     */
    public function processReturn(\XLite\Model\Payment\Transaction $transaction)
    {
        parent::processReturn($transaction);

        if (!\XLite\Core\Request::getInstance()->cancel) {
            \XLite\Core\Session::getInstance()->ec_payer_id = \XLite\Core\Request::getInstance()->PayerID;
            $this->doDoExpressCheckoutPayment();
        }
    }

    /**
     * Do initial payment and return status
     *
     * @return string
     */
    protected function doInitialPayment()
    {
        $this->transaction->createBackendTransaction($this->getInitialTransactionType());

        return $this->doDoExpressCheckoutPayment();
    }

    // }}}

    // {{{ Merchant id

    /**
     * Returns merchant id
     *
     * @return mixed
     */
    public function retrieveMerchantId()
    {
        return $this->api->getMerchantID();
    }

    // }}}

    // {{{ SetExpressCheckout

    /**
     * Perform 'SetExpressCheckout' request and get Token value from Paypal
     *
     * @param \XLite\Model\Payment\Method           $method Payment method
     * @param \XLite\Model\Payment\Transaction|null $transaction
     *
     * @return string
     */
    public function doSetExpressCheckout(
        \XLite\Model\Payment\Method $method,
        \XLite\Model\Payment\Transaction $transaction = null
    ) {
        $token = null;
        $this->transaction = $transaction;

        $responseData = $this->doRequest('SetExpressCheckout');

        if (!empty($responseData['TOKEN'])) {
            $token = $responseData['TOKEN'];

        } else {
            $this->setDetail(
                'status',
                isset($responseData['RESPMSG']) ? $responseData['RESPMSG'] : 'Unknown',
                'Status'
            );

            $transaction = \XLite\Model\Cart::getInstance()->getFirstOpenPaymentTransaction();
            if ($transaction) {
                $this->processFailTryPayment($transaction);
            }

            $this->errorMessage = isset($responseData['RESPMSG']) ? $responseData['RESPMSG'] : null;
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
        $params['INVNUM'] = $orderNumber;
        $params['CUSTOM'] = $orderNumber;

        return $params;
    }

    // }}}

    // {{{ GetExpressCheckoutDetails

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

        if (!empty($responseData) && '0' == $responseData['RESULT']) {
            $data = $responseData;
        }

        return $data;
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

    // {{{ DoExpressCheckoutPayment

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

        if ($responseData) {
            if ('0' === $responseData['RESULT']) {
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
                    'Failed: ' . $responseData['RESPMSG'],
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
        $note = $responseData['RESPMSG'];

        if (preg_match('/^Generic processor error: 10417/', $responseData['RESPMSG'])
            || preg_match('/^10417/', $responseData['RESPMSG'])) {
            $note = 'The credit card failed bank authorization. Retry the transaction using an alternative payment method from your PayPal wallet or contact PayPal Customer Service';
        }

        if (preg_match('/^Generic processor error: 10485/', $responseData['RESPMSG'])
            || preg_match('/^10485/', $responseData['RESPMSG'])) {
            $note = 'Payment has not been authorized by the user. Try to place the order again or contact PayPal Customer Service';
        }

        return $note;
    }

    /**
     * Checks if the response message can be parsed and handled properly,
     * handles it and returns the status of payment transaction.
     * Returns false if cannot handle.
     *
     * @param $responseData
     *
     * @return boolean
     */
    protected function tryHandleExpressCheckoutError($responseData)
    {
        $result = false;

        if (
            !$this->doExpressCheckoutPaymentRecursiveCall
            && isset($responseData['L_ERRORCODE0'])
            && '10419' === $responseData['L_ERRORCODE0']
        ) {
            \XLite\Core\Session::getInstance()->ec_payer_id = \XLite\Core\Request::getInstance()->PayerID;
            $this->doExpressCheckoutPaymentRecursiveCall = true;

            $result = $this->doDoExpressCheckoutPayment();
        }

        $this->doExpressCheckoutPaymentRecursiveCall = false;

        return $result;
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
        $params['INVNUM'] = $orderNumber;
        $params['CUSTOM'] = $orderNumber;

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
        $result = in_array(strtolower($response['PENDINGREASON']), ['none', 'completed']);

        if (!$result) {
            $result = (
                'authorization' == $response['PENDINGREASON']
                && \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_AUTH == $this->transaction->getType()
            );
        }

        return $result;
    }

    // }}}

    // {{{ Additional methods

    /**
     * Get return type
     *
     * @return string
     */
    public function getReturnType()
    {
        return \XLite\Model\Payment\Base\Online::RETURN_TYPE_HTTP_REDIRECT;
    }

    /**
     * Translate array of data received from Paypal to the array for updating cart
     * todo: mode to api
     *
     * @param array $paypalData Array of customer data received from Paypal
     *
     * @return array
     */
    public function prepareBuyerData($paypalData)
    {
        $countryCode = \Includes\Utils\ArrayManager::getIndex($paypalData, 'SHIPTOCOUNTRY', true);
        $country = \XLite\Core\Database::getRepo('XLite\Model\Country')
            ->findOneByCode($countryCode);

        $stateCode = \Includes\Utils\ArrayManager::getIndex($paypalData, 'SHIPTOSTATE', true);
        $state = ($country && $stateCode)
            ? \XLite\Core\Database::getRepo('XLite\Model\State')
                ->findOneByCountryAndState($country->getCode(), mb_strtoupper($stateCode, 'UTF-8'))
            : null;

        $data = [
            'shippingAddress' => [
                'name' => $paypalData['SHIPTONAME'],
                'street' => $paypalData['SHIPTOSTREET'] . (!empty($paypalData['SHIPTOSTREET2']) ? ' ' . $paypalData['SHIPTOSTREET2'] : ''),
                'country_code' => $countryCode,
                'country' => $country ?: '',
                'state_id' => $state ? $state->getStateId() : null,
                'state' => $state ?: (string) $stateCode,
                'custom_state' => $state ? $state->getState() : (string) $stateCode,
                'city' => $paypalData['SHIPTOCITY'],
                'zipcode' => $paypalData['SHIPTOZIP'],
                'phone' => isset($paypalData['PHONENUM']) ? $paypalData['PHONENUM'] : '',
            ],
        ];

        return $data;
    }

    /**
     * Get allowed currencies
     * https://www.paypalobjects.com/webstatic/en_US/developer/docs/pdf/pfp_expresscheckout_pp.pdf
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return array
     */
    protected function getAllowedCurrencies(\XLite\Model\Payment\Method $method)
    {
        return array_merge(
            parent::getAllowedCurrencies($method),
            [
                'USD', 'CAD', 'EUR', 'GBP', 'AUD',
                'CHF', 'JPY', 'NOK', 'NZD', 'PLN',
                'SEK', 'SGD', 'HKD', 'DKK', 'HUF',
                'CZK', 'BRL', 'ILS', 'MYR', 'MXN',
                'PHP', 'TWD', 'THB',
            ]
        );
    }

    // }}}
}
