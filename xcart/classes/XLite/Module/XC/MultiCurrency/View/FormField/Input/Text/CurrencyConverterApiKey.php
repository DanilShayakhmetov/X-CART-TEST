<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\FormField\Input\Text;


class CurrencyConverterApiKey extends \XLite\View\FormField\Input\Text
{
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/XC/MultiCurrency/multi_currency/parts/currency_converter_api_key.js'
        ]);
    }
    
    public function getValue()
    {
        return \XLite\Core\Config::getInstance()
            ->XC
            ->MultiCurrency
            ->currency_converter_api_key;
    }
}