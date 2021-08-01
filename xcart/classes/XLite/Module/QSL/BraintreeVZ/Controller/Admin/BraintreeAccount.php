<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\Controller\Admin;

use \XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient;

/**
 * Payment method 
 */
class BraintreeAccount extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Connect URL
     */
    protected $connectUrl = '';

    /**
     * Connect error
     */
    protected $connectError = '';

    /**
     * Curl error info
     */
    protected $curlErrNo = '';
    protected $curlError = '';

    /**
     * Check if form id is valid or not
     *
     * @return boolean
     */
    protected function isFormIdValid()
    {
        if ('oauth_return' == $this->getAction()) {
            $result = true;
        } else {
            $result = parent::isFormIdValid();
        }

        return $result;
    }

    /**
     * Get Braintree payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getPaymentMethod()
    {
        return BraintreeClient::getInstance()->getPaymentMethod();
    }

    /**
     * Check if this is Braintree payment method
     *
     * @return bool
     */
    public function isConfigured()
    {
        return BraintreeClient::getInstance()->isConfigured();
    }

    /**
     * Send request to the Intermediate Server
     *
     * @param string action
     * @param array $options
     *
     * @return string || false
     */
    protected function sendRequest($action, $options)
    {
        $ch = curl_init(BraintreeClient::getInstance()->getIntermediateServerUrl($action));

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $options);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-BraintreeVZ: 1'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        $this->curlErrNo = curl_errno($ch);
        $this->curlError = curl_error($ch);

        curl_close($ch);

        return $response;
    }

    /**
     * Return from Oauth
     *
     * @return void
     */
    protected function doActionOauthReturn()
    {
        $request = \XLite\Core\Request::getInstance();
        $client = BraintreeClient::getInstance();

        if (!empty($request->access_token)) {

            $this->getPaymentMethod()->setSetting('accessToken', $request->access_token);
            if (!empty($request->merchantId)) {
                $this->getPaymentMethod()->setSetting('merchantId', $request->merchantId);
            }
            if (!empty($request->refresh_token)) {
                $this->getPaymentMethod()->setSetting('refreshToken', $request->refresh_token);
            }

            $result = $client->synchronizeAccount();

            if ($result) {
                \XLite\Core\TopMessage::getInstance()->addInfo('Settings synchronized');
            }

            \XLite\Core\Database::getEM()->flush();

        } elseif (!empty($request->error_description)) {
            $client->processError('Braintree: ' . $request->error_description);
        } elseif (!empty($request->error)) {
            $client->processError('Braintree: ' . $request->error);
        }

        // Return back to the Braintree payment configurations page
        $this->setReturnURL(
            $this->buildURL(
                'payment_method',
                '',
                array('method_id' => $this->getPaymentMethod()->getMethodId())
            )
        );
    }

    /**
     * Obtain settings from Braintree and update store settings accordingly
     *
     * @return void
     */
    protected function doActionSynchronize()
    {
        $result = BraintreeClient::getInstance()->synchronizeAccount();

        if ($result) {
            \XLite\Core\TopMessage::getInstance()->addInfo('Settings synchronized');            
        }

        // Return back to the Braintree payment configurations page
        $this->setReturnURL(
            $this->buildURL(
                'payment_method',
                '',
                array('method_id' => BraintreeClient::getInstance()->getPaymentMethod()->getMethodId())
            )
        );
    }

    /**
     * Unlink Braintree account
     *
     * @return void
     */
    protected function doActionUnlink()
    {
        $client = BraintreeClient::getInstance();

        $options = array(
            'accessToken' => $client->getPaymentMethod()->getSetting('accessToken')
        );

        $this->sendRequest('unlink', $options);

        $client->getPaymentMethod()->setSetting('accessToken', '');
        $client->getPaymentMethod()->setSetting('merchantId', '');

        \XLite\Core\Database::getEM()->flush();

        // Return back to the Braintree account page
        $this->setReturnURL(
            $this->buildURL('braintree_account')
        );
    }

    /**
     * Refresh Braintree access token
     *
     * @return void
     */
    protected function doActionRefreshAccessToken()
    {
        $client = BraintreeClient::getInstance();

        $options = array(
            'refreshToken' => $client->getPaymentMethod()->getSetting('refreshToken')
        );

        $response = $this->sendRequest('refresh', $options);

        if (false === $response) {

            $this->connectError = 'Unable to communicate with the intermediate server.';

            if (!empty($this->curlErrNo)) {
                $this->connectError .= ' Curl error #' . $this->curlErrNo . ' ' . $this->curlError;
            }

        } else {

            $result = @json_decode($response);

            if (is_object($result)) {

                if ($result->success) {

                    $client->getPaymentMethod()->setSetting('accessToken', $result->accessToken);
                    $client->getPaymentMethod()->setSetting('refreshToken', $result->refreshToken);

                    \XLite\Core\Database::getEM()->flush();

                } else {

                    $this->connectError = $result->error . ': ' . $result->errorDescription;
                }

            } else {
                $this->connectError = 'Unable to communicate with the intermediate server.';
            }
        }

        if (!empty($this->connectError)) {
            $client->processError($this->connectError);
        } else {
            \XLite\Core\TopMessage::getInstance()->addInfo('Access token is updated');
        }

        // Return back to the Braintree account page
        $this->setReturnURL(
            $this->buildURL('braintree_account')
        );
    }

    /**
     * Get user address
     *
     * @return array
     */
    protected function getUserAddress()
    {
        $address = array();

        if ($this->getProfile()->getBillingAddress()) {

            $address = array(
                'user[firstName]'     => $this->getProfile()->getBillingAddress()->getFirstname(),
                'user[lastName]'      => $this->getProfile()->getBillingAddress()->getLastname(),
                'user[phone]'         => $this->getProfile()->getBillingAddress()->getPhone(),
                'user[streetAddress]' => $this->getProfile()->getBillingAddress()->getStreet(),
                'user[locality]'      => $this->getProfile()->getBillingAddress()->getCity(),
                'user[region]'        => BraintreeClient::getInstance()->getStateField($this->getProfile()->getBillingAddress()),
                'user[postalCode]'    => $this->getProfile()->getBillingAddress()->getZipcode(),
            );

            if ($this->getProfile()->getBillingAddress()->getCountry()) {
                $address['user[country]'] = $this->getProfile()->getBillingAddress()->getCountry()->getCode();
            }
        }

        return $address;
    }

    /**
     * Get URL for the connect button
     * (communicate with the intermediate server)
     *
     * @return void
     */ 
    protected function getConnectUrlFromIntermediateServer()
    {
        $options = array(
            'scope'                   => 'read_write',
            'state'                   => BraintreeClient::getInstance()->getOAuthState(),
            'landingPage'             => 'signup,login',

            'user[email]'             => $this->getProfile()->getEmail(),

            'business[name]'          => \XLite\Core\Config::getInstance()->Company->company_name,
            'business[registeredAs]'  => \XLite\Core\Config::getInstance()->Company->company_name,
            'business[establishedOn]' => '01-' . \XLite\Core\Config::getInstance()->Company->start_year,
            'business[streetAddress]' => \XLite\Core\Config::getInstance()->Company->location_address, 
            'business[locality]'      => \XLite\Core\Config::getInstance()->Company->location_city,
            'business[region]'        => \XLite\Core\Config::getInstance()->Company->location_state,
            'business[postalCode]'    => \XLite\Core\Config::getInstance()->Company->location_zipcode,
            'business[country]'       => \XLite\Core\Config::getInstance()->Company->location_country,
            'business[currency]'      => \XLite\Core\Config::getInstance()->General->shop_currency,
            'business[website]'       => \XLite\Core\Config::getInstance()->Company->company_website,
            'paymentMethods[0]'       => 'credit_card',
            'paymentMethods[1]'       => 'paypal',
        );

        $options += $this->getUserAddress();

        $response = $this->sendRequest('connect', $options);

        if (false === $response) {

            $this->connectError = 'Unable to communicate with the intermediate server.';

            if (!empty($this->curlErrNo)) {
                $this->connectError .= ' Curl error #' . $this->curlErrNo . ' ' . $this->curlError;
            }

        } else {

            if ('https://' == substr($response, 0, 8)) {

                // We got something like URL
                $this->connectUrl = $response;

            } else {

                $this->connectError = 'Unable to obtain URL from the intermediate server.';
            }
        }
    }

    /**
     * Get URL for the connect button
     *
     * @return string 
     */
    public function getConnectUrl()
    {
        if (empty($this->connectUrl) && empty($this->connectError)) {

            $this->getConnectUrlFromIntermediateServer();        
        }

        return $this->connectUrl;
    }

    /**
     * Get error message during communication with the intermediate server (if any)
     *
     * @return string
     */
    public function getConnectError()
    {
        if (empty($this->connectUrl) && empty($this->connectError)) {

            $this->getConnectUrlFromIntermediateServer();
        }

        return $this->connectError;
    }
}
