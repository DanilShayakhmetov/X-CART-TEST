<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View;

/**
 * Product selections page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class ProductSelections extends \XLite\View\ProductSelections
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return ['sale_discount_product_selections'];
    }

    /**
     * Defines the search panel view class
     *
     * @return string
     */
    protected function getSearchPanelView()
    {
        return '\XLite\Module\CDev\Sale\View\SearchPanel\ProductSelections\Admin\Main';
    }

    /**
     * Returns widget inner items list class
     *
     * @return string
     */
    protected function getItemsListClass()
    {
        return 'XLite\Module\CDev\Sale\View\ItemsList\Model\SaleDiscountProductSelection';
    }

}