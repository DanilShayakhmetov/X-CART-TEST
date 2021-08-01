<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Product selections page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class ProductSelections extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('product_selections'));
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'product_selections/body.twig';
    }

    /**
     * Return widget body template
     *
     * @return string
     */
    protected function getBodyTemplate()
    {
        return 'product_selections/list.twig';
    }

    /**
     * Returns widget inner items list class
     * 
     * @return string
     */
    protected function getItemsListClass()
    {
        return 'XLite\View\ItemsList\Model\ProductSelection';
    }

    /**
     * Check - search box is visible or not
     *
     * @return boolean
     */
    protected function isSearchVisible()
    {
        return true;
    }

    /**
     * Defines the search panel view class
     *
     * @return string
     */
    protected function getSearchPanelView()
    {
        return 'XLite\View\SearchPanel\ProductSelections\Admin\Main';
    }
}