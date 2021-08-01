<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductComparison\View\AddToCompare;

/**
 * Add to compare widget
 *
 * @ListChild (list="product.details.page.info.form.buttons.cart-buttons", zone="customer", weight="120")
 * @ListChild (list="product.details.page.info.form.buttons-added.cart-buttons", zone="customer", weight="120")
 */
class Product extends \XLite\Module\XC\ProductComparison\View\AddToCompare\AAddToCompare
{
    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductComparison/compare/product';
    }
}
