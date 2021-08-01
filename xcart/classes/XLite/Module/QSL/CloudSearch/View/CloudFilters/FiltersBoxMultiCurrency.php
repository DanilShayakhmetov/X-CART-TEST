<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View\CloudFilters;


use XLite;
use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;


/**
 * Cloud filters sidebar box widget
 *
 * @Decorator\Depend({"XC\MultiCurrency"})
 */
class FiltersBoxMultiCurrency extends \XLite\Module\QSL\CloudSearch\View\CloudFilters\FiltersBox implements \XLite\Base\IDecorator
{
    /**
     * Get commented widget data
     *
     * @return array
     */
    protected function getPhpToJsData()
    {
        $data = parent::getPhpToJsData();

        $selectedCurrency = MultiCurrency::getInstance()->getSelectedMultiCurrency();

        $data['currencyFormat']['rate'] = $selectedCurrency ? $selectedCurrency->getRate() : 1;

        return $data;
    }

    /**
     * Get current currency
     *
     * @return \XLite\Model\Currency
     */
    protected function getCurrency()
    {
        $selectedCurrency = MultiCurrency::getInstance()->getSelectedMultiCurrency();

        return $selectedCurrency ? $selectedCurrency->getCurrency() : parent::getCurrency();
    }
}
