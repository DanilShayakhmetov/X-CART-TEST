<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\Module\XC\ProductComparison\View;


/**
 * AddToCart
 *
 * @Decorator\Depend("XC\ProductComparison")
 */
 class ComparisonTable extends \XLite\Module\XC\ProductComparison\View\ComparisonTableAbstract implements \XLite\Base\IDecorator
{
    protected function getProductButtonWidget(\XLite\Model\Product $product)
    {
        return $product->isUpcomingProduct()
            ? $this->getWidget([], '\XLite\Module\CDev\ProductAdvisor\View\Label\ComingSoonLabel')
            : parent::getProductButtonWidget($product);
    }
}