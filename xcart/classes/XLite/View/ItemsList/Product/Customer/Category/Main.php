<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Product\Customer\Category;

use XLite\View\CacheableTrait;

/**
 * Category products list widget
 *
 * @ListChild (list="center.bottom", zone="customer", weight="200")
 */
class Main extends \XLite\View\ItemsList\Product\Customer\Category\ACategory
{
    use CacheableTrait;

    /**
     * Return name of the session cell identifier
     *
     * @return string
     */
    public function getSessionCell()
    {
        return parent::getSessionCell() . \XLite\Core\Request::getInstance()->category_id;
    }

    /**
     * Check if header is visible
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return $this->hasResults();
    }

    /**
     * Check if pager is visible
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return $this->hasResults();
    }

    /**
     * Return "empty list" catalog
     *
     * @return string
     */
    protected function getEmptyListDir()
    {
        return 'items_list';
    }

    /**
     * @return bool
     */
    protected function isDisplayWithEmptyList()
    {
        return $this->getCategoryId() !== \XLite::getController()->getRootCategoryId()
            && !$this->getCategory()->hasSubcategories();
    }
}
