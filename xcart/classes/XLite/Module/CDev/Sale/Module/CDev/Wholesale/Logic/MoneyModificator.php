<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Module\CDev\Wholesale\Logic;

use XLite\Model\Product;

/**
 * MoneyModificator: price with sale discount
 *
 * @Decorator\Depend ("CDev\Wholesale")
 */
class MoneyModificator extends \XLite\Module\CDev\Sale\Logic\MoneyModificator implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Model\AEntity $model
     * @return bool
     */
    static protected function isApplyForWholesalePrices(\XLite\Model\AEntity $model)
    {
        $product = static::getObject($model);

        if ($product instanceof Product) {
            return !$product->isWholesalePricesEnabled()
                || (
                    $product->getApplySaleToWholesale()
                    && $product->getDiscountType() === Product::SALE_DISCOUNT_TYPE_PERCENT
                )
                || !$product->getWholesalePrices()
                || $product->getWholesaleQuantity() <= 1
                || $product->isFirstWholesaleTier();
        }

        return parent::isApplyForWholesalePrices($model);
    }
}
