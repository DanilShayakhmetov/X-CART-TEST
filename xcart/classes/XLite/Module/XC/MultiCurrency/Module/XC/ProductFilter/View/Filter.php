<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Module\XC\ProductFilter\View;

/**
 * Product comparison widget
 *
 * @Decorator\Depend({"XC\ProductFilter"})
 */
class Filter extends \XLite\Module\XC\ProductFilter\View\Filter implements \XLite\Base\IDecorator
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/MultiCurrency/Module/XC/ProductFilter/product_filter.js';

        return $list;
    }

    /**
     * This data will be accessible using JS core.getCommentedData() method.
     *
     * @return array
     */
    protected function getCommentedData()
    {
        $selectedMultiCurrency = \XLite\Module\XC\MultiCurrency\Core\MultiCurrency::getInstance()->getSelectedMultiCurrency();
        $rate = $selectedMultiCurrency->getRate();

        return array_merge(
            parent::getCommentedData(),
            [
                'multicurrency_rate' => $rate,
                'store_currency_e' => \XLite::getInstance()->getCurrency()->getE(),
            ]
        );
    }
}
