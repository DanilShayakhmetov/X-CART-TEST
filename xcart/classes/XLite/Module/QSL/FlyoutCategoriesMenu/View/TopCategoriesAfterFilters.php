<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\FlyoutCategoriesMenu\View;

/**
 * Sidebar categories list
 *
 * @Decorator\Depend ("XC\ProductFilter")
 */
abstract class TopCategoriesAfterFilters extends \XLite\View\TopCategories implements \XLite\Base\IDecorator
{
    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() || 'category_filter' === $this->getTarget();
    }
}
