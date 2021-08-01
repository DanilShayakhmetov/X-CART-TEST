<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Module\CDev\Wholesale\Logic;

use XLite\Module\XC\ProductVariants\Model\ProductVariant;
use XLite\Model\Product;

/**
 * MoneyModificator: price with sale discount
 *
 * @Decorator\Depend ({"CDev\Wholesale","XC\ProductVariants","CDev\Sale"})
 */
class MoneyModificatorVariants extends \XLite\Module\CDev\Sale\Logic\MoneyModificator implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Model\AEntity $model
     * @return bool
     */
    static protected function isApplyForWholesalePrices(\XLite\Model\AEntity $model)
    {
        $variant = static::getVariantObject($model);

        if (
            $variant instanceof ProductVariant
            && $variant->isPersistent()
        ) {
            $discountType = $variant->getDefaultSale()
                ? $variant->getProduct()->getDiscountType()
                : $variant->getDiscountType();
            return !$variant->isWholesalePricesEnabled()
                || (
                    $variant->getProduct()->getApplySaleToWholesale()
                    && $discountType === Product::SALE_DISCOUNT_TYPE_PERCENT
                )
                || $variant->getProduct()->getWholesaleQuantity() <= 1
                || (
                    $variant->getDefaultPrice()
                        ? $variant->getProduct()->isFirstWholesaleTier()
                        : $variant->isFirstWholesaleTier()
                );
        }

        return parent::isApplyForWholesalePrices($model);
    }
}
