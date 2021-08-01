<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\FormField\Select;

use XLite\Module\XC\MultiCurrency\Core\CurrencyRate;

/**
 * Rate provider select class
 */
class RateProvider extends \XLite\View\FormField\Select\Regular
{
    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return \XLite\Core\Config::getInstance()->XC->MultiCurrency->rateProvider;
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            CurrencyRate::PROVIDER_NONE                        => static::t(
                'None'
            ),
            CurrencyRate::PROVIDER_FREE_CURRENCY_CONVERTER_API => static::t(
                'The Free Currency Converter API'
            ),
            CurrencyRate::PROVIDER_CURRENCY_CONVERTER_API      => static::t(
                'The Currency Converter API'
            ),
        ];
    }
}