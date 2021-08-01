<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Core;

use XLite\Module\XC\MultiCurrency\Core\RateProvider\ARateProvider;
use XLite\Module\XC\MultiCurrency\Core\RateProvider\CurrencyConverterAPI;
use XLite\Module\XC\MultiCurrency\Core\RateProvider\FreeCurrencyConverterAPI;

/**
 * Cache decorator
 */
class CurrencyRate extends \XLite\Base
{
    const PROVIDER_NONE                        = 'none';
    const PROVIDER_FREE_CURRENCY_CONVERTER_API = 'free_currency_converter_api';
    const PROVIDER_CURRENCY_CONVERTER_API      = 'currency_converter_api';

    /**
     * Rate provider
     *
     * @var \XLite\Module\XC\MultiCurrency\Core\RateProvider\ARateProvider
     */
    protected $rateProvider = null;

    /**
     * Get currency conversion rate
     *
     * @param string $to Destination currency code (alpha-3)
     *
     * @return float
     */
    public function getRate($to)
    {
        $currency = \XLite\Core\Database::getRepo('XLite\Model\Currency')->find(
            \XLite\Core\Config::getInstance()->General->shop_currency
        );

        $from = $currency->getCode();

        return $this->getRateProvider()->getRate($from, $to);
    }

    /**
     * @return ARateProvider
     */
    protected function detectRateProvider()
    {
        switch (\XLite\Core\Config::getInstance()->XC->MultiCurrency->rateProvider) {
            case static::PROVIDER_CURRENCY_CONVERTER_API:
                return new CurrencyConverterAPI();
            default:
                return new FreeCurrencyConverterAPI();
        }
    }

    /**
     * Protected constructor.
     * It's not possible to instantiate a derived class (using the "new" operator)
     * until that child class is not implemented public constructor
     *
     * @return void
     */
    protected function __construct()
    {
        parent::__construct();

        $this->rateProvider = $this->detectRateProvider();
    }

    /**
     * Get rate provider
     *
     * @return \XLite\Module\XC\MultiCurrency\Core\RateProvider\ARateProvider
     */
    protected function getRateProvider()
    {
        return $this->rateProvider;
    }
}