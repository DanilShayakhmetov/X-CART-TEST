<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Includes\Utils\ArrayManager;

/**
 * Common shipping method
 */
class Shipping extends \XLite\Base\Singleton
{
    /**
     * RuntimeDefaultAddressCache
     *
     * @var array
     */
    static $runtimeDefaultAddressCache = false;

    /**
     * List of registered shipping processors
     *
     * @var \XLite\Model\Shipping\Processor\AProcessor[]
     */
    protected static $registeredProcessors = array();

    /**
     * Flag: Ignore long shipping rates calculations mode (if true)
     * Only offline rates are calculated in this mode
     *
     * @var boolean
     */
    protected static $ignoreLongCalculations = false;

    /**
     * Set $ignoreLongCalculations variable
     *
     * @param boolean $value Mode
     *
     * @return void
     */
    public static function setIgnoreLongCalculationsMode($value)
    {
        static::$ignoreLongCalculations = $value;
    }

    /**
     * Get $ignoreLongCalculations variable value
     *
     * @return boolean
     */
    public static function isIgnoreLongCalculations()
    {
        return static::$ignoreLongCalculations;
    }

    /**
     * Register new shipping processor. All processors classes must be
     * derived from \XLite\Model\Shipping\Processor\AProcessor class
     *
     * @param string $processorClass Processor class
     *
     * @return void
     */
    public static function registerProcessor($processorClass)
    {
        if (!isset(static::$registeredProcessors[$processorClass])
            && \XLite\Core\Operator::isClassExists($processorClass)
        ) {
            static::$registeredProcessors[$processorClass] = new $processorClass();
            uasort(static::$registeredProcessors, array(\XLite\Model\Shipping::getInstance(), 'compareProcessors'));
        }
    }

    /**
     * Unregister shipping processor.
     *
     * @param string $processorClass Processor class
     *
     * @return void
     */
    public static function unregisterProcessor($processorClass)
    {
        if (isset(static::$registeredProcessors[$processorClass])) {
            unset(static::$registeredProcessors[$processorClass]);
        }
    }

    /**
     * Returns the list of registered shipping processors
     *
     * @return \XLite\Model\Shipping\Processor\AProcessor[]
     */
    public static function getProcessors()
    {
        return static::$registeredProcessors;
    }

    /**
     * @param string $processorId
     *
     * @return null|Shipping\Processor\AProcessor
     */
    public static function getProcessorObjectByProcessorId($processorId)
    {
        $result = null;

        $processors = \XLite\Model\Shipping::getInstance()->getProcessors();

        foreach ($processors as $obj) {
            if ($obj->getProcessorId() === $processorId) {
                $result = $obj;
                break;
            }
        }

        return $result;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        static::registerProcessor('\XLite\Model\Shipping\Processor\Offline');
    }

    /**
     * Return shipping rates
     *
     * @param \XLite\Logic\Order\Modifier\Shipping $modifier Shipping order modifier
     *
     * @return array
     */
    public function getRates(\XLite\Logic\Order\Modifier\Shipping $modifier)
    {
        $ratesArray = [];

        $countryAddressField = \XLite\Core\Database::getRepo('XLite\Model\AddressField')
            ->findOneBy(['serviceName' => 'country_code']);

        if ($countryAddressField && $countryAddressField->getEnabled()) {
            $address = $this->getDestinationAddress($modifier);

            /** @var \XLite\Model\Country $shippingCountry */
            $shippingCountry = \XLite\Core\Database::getRepo('XLite\Model\Country')
                ->findOneByCode($address['country'] ?? '');

            if (!$shippingCountry || !$shippingCountry->getEnabled()) {
                return [];
            }
        }

        foreach (static::$registeredProcessors as $processor) {
            if (!$this->isProcessorEnabled($processor, $modifier)) {
                continue;
            }

            if (!$this->shouldAllowLongCalculations()) {
                static::setIgnoreLongCalculationsMode(true);
            }
            // Get rates from processors
            $ratesArray[] = $this->getProcessorRates($processor, $modifier);

            if ($processor->hasError()) {
                $processor->flushErrorLog();
            }
        }

        $rates = array_merge(...$ratesArray);

        $rates = $this->applyMarkups($modifier, $rates);

        $rates = $this->postProcessRates($rates);

        return $rates;
    }

    protected function shouldAllowLongCalculations()
    {
        $refererTarget = \XLite\Core\Request::getInstance()->getAjaxRefererTarget();
        $isValidAjaxRequests = in_array($refererTarget, ['cart', 'checkout'], true);

        return (\XLite::getController()->isAJAX() && $isValidAjaxRequests)
            || \XLite::isAdminZone();
    }

    /**
     * Check if online processors are enabled
     *
     * @return bool
     */
    public function hasOnlineProcessors()
    {
        $result = false;

        foreach (static::$registeredProcessors as $processor) {
            if ($processor->getProcessorId() !== 'offline') {
                $result = true;
                break;
            }
        }

        return $result;
    }


    /**
     * Get lists of address fields required by all enabled shipping processors
     *
     * @return array
     */
    public static function getRequiredAddressFields()
    {
        $fields = array();

        foreach (static::getProcessors() as $processor) {
            if ($processor->isConfigured()) {
                $fields[$processor->getProcessorId()] = $processor->getRequiredAddressFields();
            }
        }

        return $fields;
    }

    /**
     * Get destination address
     *
     * @param \XLite\Logic\Order\Modifier\Shipping $modifier Shipping order modifier
     *
     * @return array
     */
    public function getDestinationAddress(\XLite\Logic\Order\Modifier\Shipping $modifier)
    {
        $address = null;
        $profile = $modifier->getOrder()->getProfile();

        if ($profile && $profile->getShippingAddress()) {
            // Profile is exists
            $address = $profile->getShippingAddress()->toArray();
        }

        return null === $address
            ? static::getDefaultAddress()
            : $address;
    }

    /**
     * Get default customer address
     *
     * @return array
     */
    public static function getDefaultAddress()
    {
        if (static::$runtimeDefaultAddressCache === false) {
            $config = \XLite\Core\Config::getInstance()->Shipping;
            $state = \XLite\Model\Address::getDefaultFieldValue('state');
            $country = \XLite\Model\Address::getDefaultFieldValue('country');

            static::$runtimeDefaultAddressCache = array(
                'address'      => $config->anonymous_address,
                'city'         => \XLite\Model\Address::getDefaultFieldValue('city'),
                'state'        => $state ? $state->getCode() : '',
                'custom_state' => \XLite\Model\Address::getDefaultFieldValue('custom_state'),
                'zipcode'      => \XLite\Model\Address::getDefaultFieldValue('zipcode'),
                'country'      => $country ? $country->getCode() : '',
                'type'         => $config->anonymous_address_type,
            );
        }

        return static::$runtimeDefaultAddressCache;
    }

    /**
     * Sort function for sorting processors by class
     *
     * @param \XLite\Model\Shipping\Processor\AProcessor $a First processor
     * @param \XLite\Model\Shipping\Processor\AProcessor $b Second processor
     *
     * @return integer
     */
    protected function compareProcessors($a, $b)
    {
        $bottomProcessorId = 'offline';

        $a1 = $a->getProcessorId();
        $b1 = $b->getProcessorId();

        if ($a1 === $bottomProcessorId) {
            $result = 1;

        } elseif ($b1 === $bottomProcessorId) {
            $result = -1;

        } else {
            $result = strcasecmp($a1, $b1);
        }

        return $result;
    }

    /**
     * Check if processor is enabled
     *
     * @param \XLite\Model\Shipping\Processor\AProcessor $processor Processor
     * @param \XLite\Logic\Order\Modifier\Shipping       $modifier  Modifier
     *
     * @return boolean
     */
    protected function isProcessorEnabled($processor, $modifier)
    {
        return $processor->isEnabled();
    }

    /**
     * Get rates from processor
     *
     * @param \XLite\Model\Shipping\Processor\AProcessor $processor Processor
     * @param \XLite\Logic\Order\Modifier\Shipping       $modifier  Modifier
     *
     * @return array
     */
    protected function getProcessorRates($processor, $modifier)
    {
        $rates = $processor->getRates($modifier);

        return $rates && is_array($rates) ? $rates : array();
    }

    /**
     * Apply markups to the rates and return list of modified rates
     *
     * @param \XLite\Logic\Order\Modifier\Shipping $modifier Shipping order modifier
     * @param array                                $rates    List of rates
     *
     * @return array
     */
    protected function applyMarkups($modifier, $rates)
    {
        if (!empty($rates)) {
            $markups = array();

            // Calculate markups
            foreach ($rates as $id => $rate) {
                // If markup has already been calculated for rate then continue iteration

                if (null !== $rate->getMarkup()) {
                    continue;
                }

                $processor = $rate->getMethod()->getProcessor();

                if (!isset($markups[$processor])) {
                    $markups[$processor] = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Markup')
                        ->findMarkupsByProcessor($processor, $modifier);
                }

                // Set markup to the rate
                if (isset($markups[$processor])) {
                    foreach ($markups[$processor] as $markup) {
                        if ($markup->getMethodId() == $rate->getMethodId()) {
                            $rate->setMarkup($markup);
                            $rate->setMarkupRate($markup->getMarkupValue());
                            $rates[$id] = $rate;
                        }
                    }
                }
            }
        }

        return $rates;
    }

    /**
     * Post process the list of rates
     *
     * @param array $rates List of rates
     *
     * @return array
     */
    protected function postProcessRates($rates)
    {
        $savedRatesValues = \XLite\Core\Session::getInstance()->savedRatesValues ?: array();
        $hash = array();

        if (!empty($rates)) {
            // Generate hash of rate values: methodId => baseRate
            foreach ($rates as $rate) {
                $hash[$rate->getMethodId()] = 1;
                $extraData = $rate->getExtraData();
                if ($extraData
                    && isset($extraData->cod_rate)
                    && $extraData->cod_rate
                    && $extraData->cod_rate > $rate->getBaseRate()
                ) {
                    $savedRatesValues[$rate->getMethodId()] = $rate->getBaseRate();
                }
            }

            // Remove obsolete rates from saved rate values
            foreach ($savedRatesValues as $methodId => $rateInfo) {
                if (!isset($hash[$methodId])) {
                    unset($savedRatesValues[$methodId]);
                }
            }
        } else {
            $savedRatesValues = array();
        }

        // Save rate values in the session
        \XLite\Core\Session::getInstance()->savedRatesValues = $savedRatesValues;

        return $rates;
    }

    /**
     * @param \XLite\Logic\Order\Modifier\Shipping $modifier
     * @return array
     */
    protected function getHashData(\XLite\Logic\Order\Modifier\Shipping $modifier)
    {
        return [
            'destAddress' => $this->getDestinationAddress($modifier),
            'weight' => $modifier->getWeight(),
            'countItems' => $modifier->countItems(),
            'subtotal' => $modifier->getSubtotal(),
            'discountedSubtotal' => $modifier->getDiscountedSubtotal(),
        ];
    }

    /**
     * Get hash of shipping modifier
     *
     * @param \XLite\Logic\Order\Modifier\Shipping $modifier
     * @return string
     */
    public function getHash(\XLite\Logic\Order\Modifier\Shipping $modifier)
    {
        return ArrayManager::md5($this->getHashData($modifier));
    }
}
