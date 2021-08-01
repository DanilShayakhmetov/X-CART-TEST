<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Model\Shipping\PBAPI;

use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Request\CreateShipment;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Request\DeleteShipment;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Request\MerchantInfo;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Request\OAuthToken;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Request\Rates;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Request\RequestException;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Request\Tracking;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\TokenStorage\FactoryException;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\TokenStorage\TmpVar;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\TokenStorage\TokenStorageException;

class RequestFactory
{
    const MODE_PRODUCTION = 'production';
    const MODE_SANDBOX    = 'sandbox';

    /**
     * @var array
     */
    protected $configuration = [
        self::MODE_PRODUCTION => [
            'developerId' => '46660746',
            'clientId'    => 'Zu3aq8bDInTkzpAGYmSqVJYn88G2Ic9Q',
            'secret'      => 'kFqSwL7SArPo68rb',
        ],
        self::MODE_SANDBOX    => [
            'developerId' => '46660746',
            'clientId'    => '9bWL8QBHOqyV8A7e01llnBQlpqnBpjFL',
            'secret'      => 'gmpjwQDeZuOvzTGU',
        ],
    ];

    /**
     * @var string
     */
    protected $mode = self::MODE_PRODUCTION;

    /**
     * @var ITokenStorage
     */
    protected $tokenStorage;

    /**
     * RequestFactory constructor.
     *
     * @param string             $mode
     * @param ITokenStorage|null $tokenStorage
     */
    public function __construct($mode = self::MODE_PRODUCTION, ITokenStorage $tokenStorage = null)
    {
        $this->mode          = $mode;
        $this->tokenStorage  = $tokenStorage === null ? new TmpVar() : $tokenStorage;
    }

    public function dropToken()
    {
        return $this->tokenStorage->setToken(null);
    }

    /**
     * @return IRequest
     * @throws FactoryException
     */
    public function createOAuthTokenRequest()
    {
        $configuration = $this->configuration[$this->mode];

        if (!isset($configuration['clientId'], $configuration['secret'])) {

            throw new FactoryException('Configuration is invalid or missed');
        }

        return new OAuthToken($this->getEndPoint(), $configuration['clientId'], $configuration['secret']);
    }

    /**
     * @param $emailId
     *
     * @return MerchantInfo
     * @throws FactoryException
     */
    public function createMerchantInfoRequest($emailId)
    {
        $configuration = $this->configuration[$this->mode];

        if (!isset($configuration['developerId'])) {

            throw new FactoryException('Configuration is invalid or missed');
        }

        return new MerchantInfo($this->getEndPoint(), $this->getToken(), $configuration['developerId'], $emailId);
    }

    /**
     * @param $inputData
     *
     * @return Rates
     * @throws FactoryException
     */
    public function createRatesRequest($inputData)
    {
        return new Rates($this->getEndPoint(), $this->getToken(), $inputData);
    }

    /**
     * @param $transactionId
     * @param $inputData
     *
     * @return CreateShipment
     * @throws FactoryException
     */
    public function createCreateShipmentRequest($transactionId, $inputData)
    {
        return new CreateShipment($this->getEndPoint(), $this->getToken(), $transactionId, $inputData);
    }

    /**
     * @param $transactionId
     * @param $shipmentId
     *
     * @return DeleteShipment
     * @throws FactoryException
     */
    public function createDeleteShipmentRequest($transactionId, $shipmentId)
    {
        return new DeleteShipment($this->getEndPoint(), $this->getToken(), $transactionId, $shipmentId);
    }

    /**
     * @param $trackingNumber
     *
     * @return Tracking
     * @throws FactoryException
     */
    public function createTrackingRequest($trackingNumber)
    {
        return new Tracking($this->getEndPoint(), $this->getToken(), $trackingNumber);
    }

    /**
     * @return string
     */
    public function getRegistrationURL()
    {
        $configuration = $this->configuration[$this->mode];

        return $this->mode === self::MODE_PRODUCTION
            ? 'https://www.pbshippingmerchant.pitneybowes.com/home?developerID=' . $configuration['developerId']
            : 'https://developer.pbshippingmerchant.pitneybowes.com/home?developerID=' . $configuration['developerId'];
    }

    /**
     * @return string
     */
    protected function getEndPoint()
    {
        return $this->mode === self::MODE_PRODUCTION
            ? 'https://api.pitneybowes.com'
            : 'https://api-sandbox.pitneybowes.com';
    }

    /**
     * @param $type
     *
     * @return IRequest
     */
    protected function createRequest($type)
    {

    }

    protected function getConfiguration()
    {
        if ($this->mode === self::MODE_PRODUCTION) {
            return [
                'developerId' => '',
                'clientId'    => '',
                'secret'      => '',
            ];
        }

        return [
            'developerId' => '',
            'clientId'    => '',
            'secret'      => '',
        ];
    }

    /**
     * @return string
     * @throws FactoryException
     */
    protected function getToken()
    {
        try {

            return $this->tokenStorage->getToken();

        } catch (TokenStorageException $e) {
        }

        try {
            $request   = $this->createOAuthTokenRequest();
            $tokenData = $request->performRequest();

            $issuedAt  = isset($tokenData['issuedAt']) ? (int) ($tokenData['issuedAt'] / 1000) : \LC_START_TIME;
            $expiresIn = isset($tokenData['expiresIn']) ? (int) $tokenData['expiresIn'] : (10 * 60 * 60);

            $this->tokenStorage->setExpiration($issuedAt + $expiresIn);

            $token = $tokenData['access_token'];
            $this->tokenStorage->setToken($tokenData['access_token']);

            return $token;

        } catch (RequestException $e) {

            throw new FactoryException($e);
        }
    }
}
