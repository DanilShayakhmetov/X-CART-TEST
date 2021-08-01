<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Model\Product;
use XLite\Module\QSL\Backorder\Model\Product as BackorderProduct;
use XLite\Module\XC\ProductVariants\Model\ProductVariant;


/**
 * CloudSearch store-side API methods
 *
 * @Decorator\Depend ({"QSL\Backorder", "XC\ProductVariants"})
 */
abstract class StoreApiBackorderProductVariants extends \XLite\Module\QSL\CloudSearch\Core\StoreApi implements \XLite\Base\IDecorator
{
    /**
     * Get product variant stock status
     *
     * @param Product        $product
     * @param ProductVariant $variant
     *
     * @return string
     */
    protected function getVariantStockStatus(Product $product, ProductVariant $variant)
    {
        /** @var BackorderProduct $product */
        if ($product->getIsAvailableForBackorder()) {
            return StoreApiBackorder::INV_BACKORDER;
        }

        return parent::getVariantStockStatus($product, $variant);
    }
}
