<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Model\Product;
use XLite\Module\QSL\CloudSearch\Main;


/**
 * CloudSearch store-side API methods
 *
 * @Decorator\Depend ({"CDev\VAT"})
 */
abstract class StoreApiVAT extends \XLite\Module\QSL\CloudSearch\Core\StoreApi implements \XLite\Base\IDecorator
{
    /**
     * Get product price.
     *
     * @param Product $product
     *
     * @return float
     */
    protected function getProductPrice(Product $product)
    {
        if (Main::isCloudFiltersEnabled()) {
            $id = $product->getProductId();

            if (!isset($this->priceCache[$id])) {
                $this->priceCache[$id] = $product->getDisplayPrice();
            }

            return $this->priceCache[$id];
        }

        return parent::getProductPrice($product);
    }
}
