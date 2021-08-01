<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Bestsellers\View;

use XLite\View\CacheableTrait;

/**
 * abstract Bestsellers view
 */
abstract class ABestsellers extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    use CacheableTrait;

    /**
     * Widget parameter names
     */
    const PARAM_ROOT_ID     = 'rootId';
    const PARAM_USE_NODE    = 'useNode';
    const PARAM_CATEGORY_ID = 'category_id';

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     */
    public function __construct(array $params = array())
    {
        parent::__construct($params);

        unset($this->sortByModes[static::SORT_BY_MODE_AMOUNT]);
    }

    protected function getSortByModeDefault()
    {
        return static::SORT_BY_MODE_BOUGHT;
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Bestsellers';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_USE_NODE => new \XLite\Model\WidgetParam\TypeCheckbox(
                'Show products only for current category', true, true
            ),
            static::PARAM_ROOT_ID => new \XLite\Model\WidgetParam\ObjectId\Category(
                'Root category Id', 0, true, true
            ),
            static::PARAM_CATEGORY_ID => new \XLite\Model\WidgetParam\ObjectId\Category(
                'Category ID', 0, false
            ),
        );
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
        $searchCase->{\XLite\Model\Repo\Product::SEARCH_BESTSELLERS} = true;
        $searchCase->{\XLite\Model\Repo\Product::P_SEARCH_IN_SUBCATS} = true;

        return $searchCase;
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-bestsellers';
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();
        $params[] = \XLite\Core\Request::getInstance()->category_id;

        return $params;
    }
}
