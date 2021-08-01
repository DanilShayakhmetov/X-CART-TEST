<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Module\XC\ProductFilter\View\Filter;

/**
 * Price range widget
 *
 * @Decorator\Depend({"XC\ProductFilter"})
 */
class PriceRange extends \XLite\Module\XC\ProductFilter\View\Filter\PriceRange implements \XLite\Base\IDecorator
{
    /**
     * Return min price
     *
     * @return float
     */
    public function getMinPrice()
    {
        $minPrice = \XLite\Core\Database::getRepo('\XLite\Model\Product')->search(
            $this->getMinPriceCondition(),
            \XLite\Model\Repo\Product::SEARCH_MODE_SCALAR
        );

        $selectedMultiCurrency = \XLite\Module\XC\MultiCurrency\Core\MultiCurrency::getInstance()->getSelectedMultiCurrency();
        $rate = $selectedMultiCurrency->getRate();

        return number_format(
            $minPrice * $rate,
            $selectedMultiCurrency->getCurrency()->getE(),
            '.',
            ''
        );
    }

    /**
     * Return max value
     *
     * @return float
     */
    public function getMaxPrice()
    {
        $maxPrice = \XLite\Core\Database::getRepo('\XLite\Model\Product')->search(
            $this->getMaxPriceCondition(),
            \XLite\Model\Repo\Product::SEARCH_MODE_SCALAR
        );

        $selectedMultiCurrency = \XLite\Module\XC\MultiCurrency\Core\MultiCurrency::getInstance()->getSelectedMultiCurrency();
        $rate = $selectedMultiCurrency->getRate();

        return number_format(
            $maxPrice * $rate,
            $selectedMultiCurrency->getCurrency()->getE(),
            '.',
            ''
        );
    }

    /**
     * Get currency symbol
     *
     * @return string
     */
    public function getSymbol()
    {
        $currency = \XLite\Module\XC\MultiCurrency\Core\MultiCurrency::getInstance()->getSelectedCurrency();

        return $currency ? $currency->getCurrencySymbol() : '';
    }

    /**
     * Get value
     *
     * @return array
     */
    protected function getValue()
    {
        $filterValues = $this->getFilterValues();

        $result = [];

        if (isset($filterValues['price'])
            && is_array($filterValues['price'])
        ) {
            $selectedMultiCurrency = \XLite\Module\XC\MultiCurrency\Core\MultiCurrency::getInstance()->getSelectedMultiCurrency();
            $rate = $selectedMultiCurrency->getRate();
            $e = $selectedMultiCurrency->getCurrency()->getE();

            if (isset($filterValues['price'][0])) {
                $filterValues['price'][0] = round($filterValues['price'][0] * $rate, $e);
            }
            if (isset($filterValues['price'][1])) {
                $filterValues['price'][1] = round($filterValues['price'][1] * $rate, $e);
            }

            $result = $filterValues['price'];
        }

        return $result;
    }
}
