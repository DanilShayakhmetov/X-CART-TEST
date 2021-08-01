<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Core\CommonCell;
use XLite\Core\Database;
use XLite\Model\Product;
use XLite\Module\QSL\Make\Main as MakeMain;


/**
 * CloudSearch store-side API methods
 *
 * @Decorator\Depend ({"QSL\Make"})
 */
class StoreApiMMY extends \XLite\Module\QSL\CloudSearch\Core\StoreApi implements \XLite\Base\IDecorator
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

        $fitments = Database::getRepo(MakeMain::getLastLevelProductRepository())->search(new CommonCell([
            'productId' => $product->getProductId(),
        ]));

        if ($fitments) {
            $conditions['mmy'] = [];

            foreach ($fitments as $fitment) {
                $conditions['mmy'][] = 'level_' . $fitment->getLevel()->getId();
            }
        }

        return $conditions;
    }
}
