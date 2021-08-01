<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\View\Product;

/**
 * @Decorator\After("XC\FacebookMarketing")
 * @Decorator\Depend("XC\ProductVariants")
 */
class ListItemProductVariants extends \XLite\View\Product\ListItem implements \XLite\Base\IDecorator
{
    /**
     * @return string
     */
    protected function getFacebookPixelProductSku()
    {
        $product = $this->getProduct();

        if ($product->hasVariants()) {
            $variant = $product->getDefaultVariant();

            return $variant->getSku() ?: $variant->getVariantId();
        }

        return $product->getSku();
    }
}