<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Model\Product;
use XLite\Module\QSL\Backorder\Model\Product as BackorderProduct;


/**
 * CloudSearch store-side API methods
 *
 * @Decorator\Depend ({"QSL\Backorder"})
 */
abstract class StoreApiBackorder extends \XLite\Module\QSL\CloudSearch\Core\StoreApi implements \XLite\Base\IDecorator
{
    const INV_BACKORDER = 'backorder';

    /**
     * Get product stock status
     *
     * @param Product $product
     *
     * @return string
     */
    protected function getProductStockStatus(Product $product)
    {
        /** @var BackorderProduct $product */
        if ($product->getIsAvailableForBackorder()) {
            return self::INV_BACKORDER;
        }

        return parent::getProductStockStatus($product);
    }
}
