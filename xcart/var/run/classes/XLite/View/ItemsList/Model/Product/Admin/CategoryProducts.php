<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Product\Admin;

/**
 * Category products
 */
class CategoryProducts extends \XLite\View\ItemsList\Model\Product\Admin\Search
{
    const SORT_BY_MODE_POSITION = 'cp.orderby';

    protected function wrapWithFormByDefault()
    {
        return true;
    }

    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('category_products'));
    }

    protected function getSearchPanelClass()
    {
        return null;
    }

    protected function getFormTarget()
    {
        return 'category_products';
    }

    protected function getFormParams()
    {
        return array_merge(
            parent::getFormParams(),
            array(
                'id' => $this->getCategoryId(),
            )
        );
    }

    protected function getCreateURL()
    {
        return $this->buildURL(
            'product',
            '',
            array(
                'category_id' => $this->getCategoryId(),
            )
        );
    }

    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();
        $result->{static::PARAM_CATEGORY_ID} = $this->getCategoryId();
        $result->{static::PARAM_SEARCH_IN_SUBCATS} = false;

        return $result;
    }

    protected function isExportable()
    {
        return true;
    }

    protected function defineColumns()
    {
        $list = parent::defineColumns();
        unset($list['category']);
        foreach ($list as $name => $info) {
            unset($list[$name][static::COLUMN_SORT]);
        }
        return $list;
    }

    protected function getMovePositionWidgetClassName()
    {
        return 'XLite\View\FormField\Inline\Input\Text\Position\CategoryProducts\Move';
    }

    protected function getOrderByWidgetClassName()
    {
        return 'XLite\View\FormField\Inline\Input\Text\Position\CategoryProducts\OrderBy';
    }

    protected function getPositionColumnValue(\XLite\Model\Product $product)
    {
        return $product->getPosition($this->getCategoryId());
    }

    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();
        $this->commonParams['id'] = $this->getCategoryId();

        return $this->commonParams;
    }

    protected function getBlankItemsListDescription()
    {
        return static::t('itemslist.admin.product.blank');
    }

    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\Product\Admin\CategoryProducts';
    }

    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_POSITION;
    }
}
