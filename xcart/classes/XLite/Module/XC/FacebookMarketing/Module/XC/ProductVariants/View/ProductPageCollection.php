<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Module\XC\ProductVariants\View;

/**
 * Product page widgets collection
 *
 * @Decorator\Depend("XC\ProductVariants")
 * @Decorator\After("XC\ProductVariants")
 */
class ProductPageCollection extends \XLite\View\ProductPageCollection implements \XLite\Base\IDecorator
{
    /**
     * Register the view classes collection
     *
     * @return array
     */
    protected function defineWidgetsCollection()
    {
        $widgets = parent::defineWidgetsCollection();

        if ($this->getProduct()->hasVariants()) {
            $widgets = array_merge(
                $widgets,
                ['XLite\Module\XC\FacebookMarketing\View\Product\Details\Customer\PixelValue']
            );
        }

        return array_unique($widgets);
    }
}
