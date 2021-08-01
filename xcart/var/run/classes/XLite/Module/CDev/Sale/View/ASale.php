<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View;

/**
 * Sale products abstract widget class
 *
 */
abstract class ASale extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    const PARAM_CATEGORY_ID = 'category_id';
    const SORT_BY_MODE_SALE = \XLite\Model\Repo\Product::PERCENT_CALCULATED_FIELD;

    /**
     * Widget target
     */
    const WIDGET_TARGET_SALE_PRODUCTS = 'sale_products';

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->sortByModes = [
                static::SORT_BY_MODE_SALE => 'Recommended',
            ] + $this->sortByModes;
    }

    protected function getSingleOrderSortByFields()
    {
        return array_merge(parent::getSingleOrderSortByFields(), [
            static::SORT_BY_MODE_SALE => static::SORT_ORDER_DESC,
        ]);
    }

    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_SALE;
    }

    protected function getSortByFields()
    {
        return [
                'sale' => static::SORT_BY_MODE_SALE,
            ] + parent::getSortByFields();
    }

    /**
     * Return target to retrive this widget from AJAX
     *
     * @return string
     */
    protected static function getWidgetTarget()
    {
        return static::WIDGET_TARGET_SALE_PRODUCTS;
    }

    /**
     * Returns CSS classes for the container element
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' sale-products';
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getHead()
    {
        return static::t('Sale');
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\Module\CDev\Sale\View\Pager\Pager';
    }

    /**
     * Default search conditions
     *
     * @param  \XLite\Core\CommonCell $searchCase Search case
     *
     * @return \XLite\Core\CommonCell
     */
    protected function postprocessSearchCase(\XLite\Core\CommonCell $searchCase)
    {
        $searchCase = parent::postprocessSearchCase($searchCase);
        $searchCase->{\XLite\Module\CDev\Sale\Model\Repo\Product::P_PARTICIPATE_SALE} = true;

        return $searchCase;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->hasResults();
    }

    /**
     * Get max number of products displayed in block
     *
     * @return integer
     */
    protected function getMaxCountInBlock()
    {
        return (int)\XLite\Core\Config::getInstance()->CDev->Sale->sale_max_count_in_block ?: 3;
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-sale-products';
    }
}
