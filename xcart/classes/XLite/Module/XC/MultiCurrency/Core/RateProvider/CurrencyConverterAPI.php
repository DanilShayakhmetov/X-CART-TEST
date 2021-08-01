<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Core\RateProvider;

/**
 * Currency converter (https://www.currencyconverterapi.com/)
 */
class CurrencyConverterAPI extends FreeCurrencyConverterAPI
{
    protected $url = 'https://api.currencyconverterapi.com/api/v6/';
}
