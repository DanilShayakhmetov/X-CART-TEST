<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Model\Product;

/**
 * CloudSearch store-side API methods
 *
 * @Decorator\Depend ({"CDev\Sale"})
 */
abstract class StoreApiSale extends \XLite\Module\QSL\CloudSearch\Core\StoreApiAbstract implements \XLite\Base\IDecorator
{
    /**
     * Get "conditions" that can be used to restrict the results when searching.
     *
     * This is different from "attributes" which are used to construct full-fledged filters (CloudFilters).
     *
     * @param Product $product
     * @return array
     */
    protected function getProductConditions(Product $product)
    {
        $conditions = parent::getProductConditions($product);

        if ($product->getParticipateSale()) {
            $conditions['type'][] = 'sale';
        }

        return $conditions;
    }
}
