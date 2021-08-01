<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Module\XC\ProductFilter\Controller\Customer;

/**
 * Category filter
 *
 * @Decorator\Depend({"XC\ProductFilter"})
 */
class CategoryFilter extends \XLite\Module\XC\ProductFilter\Controller\Customer\CategoryFilter implements \XLite\Base\IDecorator
{
    /**
     * Do action filter
     *
     * @return void
     */
    protected function doActionFilter()
    {
        $filterData = \XLite\Core\Request::getInstance()->filter;

        $multiCurrencyCore = \XLite\Module\XC\MultiCurrency\Core\MultiCurrency::getInstance();
        if (isset($filterData['price'])
            && is_array($filterData['price'])
            && !$multiCurrencyCore->isDefaultCurrencySelected()
        ) {
            $selectedMultiCurrency = $multiCurrencyCore->getSelectedMultiCurrency();
            $rate = $selectedMultiCurrency->getRate();

            if (isset($filterData['price'][0])) {
                $filterData['price'][0] = (float)$filterData['price'][0] / $rate;
            }
            if (isset($filterData['price'][1])) {
                $filterData['price'][1] = (float)$filterData['price'][1] / $rate;
            }

            \XLite\Core\Request::getInstance()->filter = $filterData;
        }

        parent::doActionFilter();
    }
}
