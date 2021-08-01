<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Model\Shipping\Processor;

use XLite\Model\Shipping\Rate;
use XLite\Module\CDev\USPS\Main;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Helper;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Request\RequestException;
use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\TokenStorage\FactoryException;

/**
 * USPS rates vis Pitney Bowes API shipping processor model
 * API documentation: https://developer2.pitneybowes.com/docs/shipping/v1/en/PB_Shipping_Services_APIs-Reference_version_1.03.pdf
 *
 * @version 1.03
 */
class PB extends \XLite\Model\Shipping\Processor\AProcessor
{
    /**
     * Returns processor Id
     *
     * @return string
     */
    public function getProcessorId()
    {
        return 'usps';
    }

    /**
     * Returns url for sign up
     *
     * @return string
     */
    public function getSettingsURL()
    {
        return \XLite\Module\CDev\USPS\Main::getSettingsForm();
    }

    /**
     * Returns true if USPS module is configured
     *
     * @return boolean
     */
    public function isConfigured()
    {
        $config = $this->getConfiguration();

        return ($config->dataProvider === 'USPS' && $config->userid && $config->server_url)
            || ($config->dataProvider === 'pitneyBowes' && $config->pbShipperId);
    }

    /**
     * Check test mode
     *
     * @return boolean
     */
    public function isTestMode()
    {
        $config = $this->getConfiguration();

        return (bool) $config->pbSandbox;
    }

    /**
     * Returns true as USPS shipping module has not any predefined shipping methods
     *
     * @param string $state Method state flag
     *
     * @return boolean
     */
    protected function hasMethods($state = self::STATE_ENABLED_ONLY)
    {
        return true;
    }

    /**
     * Returns shipping rates by shipping order modifier (used on checkout)
     *
     * @param array|\XLite\Logic\Order\Modifier\Shipping $inputData   Shipping order modifier or array of data for request
     * @param boolean                                    $ignoreCache Flag: if true then do not get rates from cache OPTIONAL
     *
     * @return array
     */
    public function getRates($inputData, $ignoreCache = false)
    {
        $configuration = $this->getConfiguration();
        $rates         = [];

        if ($configuration->dataProvider === 'pitneyBowes') {
            $rates = parent::getRates($inputData, $ignoreCache);
            if ($rates) {
                $this->setError();
            }
        }

        return $rates;
    }

    /**
     * Returns prepared delivery time
     *
     * @param \XLite\Model\Shipping\Rate $rate
     *
     * @return string|null
     */
    public function prepareDeliveryTime(\XLite\Model\Shipping\Rate $rate)
    {
        $days = $rate->getDeliveryTime();

        if ($days !== null) {
            return static::t('X days', ['days' => $days]);
        }

        return null;
    }

    /**
     * Performs request to carrier server and returns array of rates
     *
     * @param array   $data        Array of request parameters
     * @param boolean $ignoreCache Flag: if true then do not get rates from cache
     *
     * @return \XLite\Model\Shipping\Rate[]
     */
    protected function performRequest($data, $ignoreCache)
    {
        $rates = $this->retrieveRates($data, $ignoreCache);

        $hasCOD   = $this->hasCODFlag($data);
        $dtoRates = $hasCOD
            ? $rates
            : $this->retrieveRates($this->addCODFlag($data), $ignoreCache);

        foreach ($rates as $rate) {
            if ($hasCOD) {
                $dtoRate = null;
                foreach ($dtoRates as $dtoRateTmp) {
                    if ($rate->getMethodId() === $dtoRateTmp->getMethodId()) {
                        $dtoRate = $dtoRateTmp;
                        break;
                    }
                }

                if ($dtoRate) {
                    $extraData                = $rate->getExtraData() ?: new \XLite\Core\CommonCell();
                    $extraData->cod_supported = true;
                    $extraData->cod_rate      = $dtoRate->getBaseRate();
                    $rate->setExtraData($extraData);
                }
            } else {
                $extraData                = $rate->getExtraData() ?: new \XLite\Core\CommonCell();
                $extraData->cod_supported = true;
                $extraData->cod_rate      = $rate->getBaseRate();
                $rate->setExtraData($extraData);
            }
        }

        return $rates;
    }

    /**
     * @param array   $data
     * @param boolean $ignoreCache
     *
     * @return \XLite\Model\Shipping\Rate[]
     */
    protected function retrieveRates($data, $ignoreCache)
    {
        $result = [];

        $cacheKey = serialize($data);

        $rates = [];
        if (!$ignoreCache) {
            $rates = $this->getDataFromCache($cacheKey);
        }

        if ($rates === null) {
            try {
                $requestFactory            = Main::getRequestFactory($this->getConfiguration());
                $ratesRequest              = $requestFactory->createRatesRequest(null);
                $this->apiCommunicationLog = $this->apiCommunicationLog ?: [];

                foreach ($data as $packageRequest) {
                    $ratesRequest->setInputData($packageRequest);
                    $rates[]                     = $ratesRequest->performRequest();
                    $this->apiCommunicationLog[] = $ratesRequest->getCommunication();
                }

                $rates = $this->mergeRates($rates);

                $this->saveDataInCache($cacheKey, $rates);

            } catch (FactoryException $e) {
                $this->setError($e->getMessage());
                Main::log($e->getMessage());

            } catch (RequestException $e) {
                $this->saveDataInCache($cacheKey, []);
                $this->setError($e->getMessage());
                Main::log($e->getMessage());
            }
        }

        if (isset($rates['rates'])) {
            $config       = $this->getConfiguration();
            $currencyRate = (float) $config->currency_rate;
            $currencyRate = (0 < $currencyRate ? $currencyRate : 1);

            foreach ($rates['rates'] as $rateData) {
                $rate   = new Rate();
                $method = $this->getMethodByCode($rateData['serviceId']);
                if ($method) {
                    $rate->setMethod($method);
                    $rateValue = (float) $rateData['totalCarrierCharge'];

                    $rate->setBaseRate(($rateValue) * $currencyRate);

                    if (isset($rateData['deliveryCommitment'])) {
                        $rate->setDeliveryTime($rateData['deliveryCommitment']['maxEstimatedNumberOfDays']);
                    }

                    $result[] = $rate;
                }
            }
        }

        return $result;
    }

    /**
     * @param array $response
     *
     * @return array
     */
    protected function mergeRates($response)
    {
        $result = array_shift($response);

        foreach ($response as $rates) {
            $previous = $result;
            $result['rates']   = [];

            foreach ($rates['rates'] as $rate) {
                $previousRate = $this->findRateInResponse($previous, $rate['serviceId']);
                if ($previousRate) {
                    $rate['totalCarrierCharge'] += $previousRate['totalCarrierCharge'];
                    $rate['baseCharge']         += $previousRate['baseCharge'];

                    $specialServices = isset($rate['specialServices']) ? $rate['specialServices'] : [];
                    $rate['specialServices'] = [];
                    foreach ($specialServices as $specialService) {
                        $previousSpecialService = $this->findSpecialServiceInRate($previousRate, $specialService['specialServiceId']);
                        if ($previousSpecialService) {
                            $specialService['fee'] += $previousSpecialService['fee'];
                            $rate['specialServices'][] = $specialService;
                        }
                    }

                    $result['rates'][] = $rate;
                }
            }
        }

        return $result;
    }

    /**
     * @param array  $response
     * @param string $serviceId
     *
     * @return array|null
     */
    protected function findRateInResponse($response, $serviceId)
    {
        foreach ($response['rates'] as $rate) {
            if ($rate['serviceId'] === $serviceId) {
                return $rate;
            }
        }

        return null;
    }

    /**
     * @param array  $rate
     * @param string $specialServiceId
     *
     * @return array|null
     */
    protected function findSpecialServiceInRate($rate, $specialServiceId)
    {
        $specialServices = isset($rate['specialServices']) ? $rate['specialServices'] : [];

        foreach ($specialServices as $specialService) {
            if ($specialService['specialServiceId'] === $specialServiceId) {
                return $specialService;
            }
        }

        return null;
    }

    /**
     * Prepare input data from array
     *
     * @param array $inputData Array of input data (from test controller)
     *
     * @return array
     */
    protected function prepareDataFromArray(array $inputData)
    {
        $package = $inputData['packages'][0];
        list($package['length'], $package['width'], $package['height']) = $this->getConfiguration()->dimensions;

        $inputData['packages'][0] = $package;

        return $inputData;
    }

    /**
     * Prepare input data from order modifier
     *
     * @param \XLite\Logic\Order\Modifier\Shipping $inputData Shipping order modifier
     *
     * @return array
     */
    protected function prepareDataFromModifier(\XLite\Logic\Order\Modifier\Shipping $inputData)
    {
        $result = [];

        $sourceAddress = $inputData->getOrder()->getSourceAddress();
        $sourceCountry = $sourceAddress->getCountryCode();
        if ('US' === $sourceCountry || 'PR' === $sourceCountry) {
            $result['srcAddress'] = $sourceAddress->toArray();
            $result['dstAddress'] = \XLite\Model\Shipping::getInstance()->getDestinationAddress($inputData);
            $result['packages']   = $this->getPackages($inputData);

            // Detect if COD payment method has been selected by customer on checkout
            if ($inputData->getOrder()->getFirstOpenPaymentTransaction()) {
                $paymentMethod = $inputData->getOrder()->getPaymentMethod();

                if ($paymentMethod && 'COD_USPS' === $paymentMethod->getServiceName()) {
                    $result['cod_enabled'] = true;
                }
            }
        }

        return $result;
    }

    /**
     * Post process input data
     *
     * @param array $inputData Prepared input data
     *
     * @return array
     */
    protected function postProcessInputData(array $inputData)
    {
        $result = [];
        $config = $this->getConfiguration();

        foreach ($package = $inputData['packages'] as $package) {
            $packageRequest = [];

            $inputData['srcAddress']['country'] = 'US';

            $packageRequest['fromAddress'] = Helper::convertArrayAddressToPBAddress($inputData['srcAddress']);
            $packageRequest['toAddress']   = Helper::convertArrayAddressToPBAddress($inputData['dstAddress']);

            $ounces = Helper::toOunces($package['weight'], \XLite\Core\Config::getInstance()->Units->weight_unit);

            $dim = isset($package['box'])
                ? $package['box']
                : $package;

            $dim = array_map('floatval', [$dim['length'], $dim['width'], $dim['height']]);
            sort($dim);
            $length = array_pop($dim);
            $width  = array_pop($dim);
            $height = array_pop($dim);

            $packageRequest['parcel'] = [
                'weight'    => [
                    'weight'            => $ounces,
                    'unitOfMeasurement' => 'OZ',
                ],
                'dimension' => [
                    'length'            => $length,
                    'width'             => $width,
                    'height'            => $height,
                    'unitOfMeasurement' => 'IN',
                ],
            ];

            $packageType = $packageRequest['toAddress']['countryCode'] === 'US'
                ? $config->pb_domestic_parcel_type
                : $config->pb_international_parcel_type;

            $rate = [
                'carrier'             => 'USPS',
                'inductionPostalCode' => $packageRequest['fromAddress']['postalCode'],
                'parcelType'          => $packageType,
                'specialServices'     => [],
            ];

            $packageRequest['rates'] = [$rate];

            if (isset($inputData['cod_enabled']) && $inputData['cod_enabled']) {
                $packageRequest = $this->addCODFlag($packageRequest);
            }

            $result[] = $packageRequest;
        }

        return parent::postProcessInputData($result);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function addCODFlag($data)
    {
        foreach ($data as $index => $packageRequest) {
            $rate                      = $packageRequest['rates'][0];
            $rate['specialServices'][] = [
                'specialServiceId' => 'COD',
                'inputParameters'  => [
                    [
                        'name'  => 'INPUT_VALUE',
                        'value' => 1,
                    ],
                ],
            ];

            $data[$index]['rates'] = [$rate];
        }

        return $data;
    }

    protected function hasCODFlag($data)
    {

        $rate = $data[0]['rates'][0];
        foreach ($rate['specialServices'] as $specialService) {
            if ($specialService['specialServiceId'] === 'COD') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get package limits
     *
     * @return array
     */
    protected function getPackageLimits()
    {
        $limits = parent::getPackageLimits();

        $config = $this->getConfiguration();

        $limits['weight'] = $config->max_weight;
        list($limits['length'], $limits['width'], $limits['height']) = $config->dimensions;

        return $limits;
    }

    /**
     * @param mixed $data
     */
    protected function log($data)
    {
        Main::log($data);
    }

    /**
     * Fetch methods from database
     *
     * @return \XLite\Model\Shipping\Method[]
     */
    protected function fetchMethods()
    {
        if (null === $this->methods) {
            $repo          = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method');
            $this->methods = $repo->findBy(['carrier' => 'pb_usps']);
        }

        return $this->methods ?: [];
    }
}
