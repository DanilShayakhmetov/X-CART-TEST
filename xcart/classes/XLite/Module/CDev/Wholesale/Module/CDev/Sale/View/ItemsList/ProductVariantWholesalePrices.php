<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Module\CDev\Sale\View\ItemsList;

/**
 * Wholesale prices items list (product variant)
 *
 * @Decorator\Depend("CDev\Sale")
 */
class ProductVariantWholesalePrices extends \XLite\Module\CDev\Wholesale\View\ItemsList\ProductVariantWholesalePrices implements \XLite\Base\IDecorator
{
    /**
     * @return bool
     */
    protected function isOnAbsoluteSale()
    {
        $hasSpecificSale = !$this->getProductVariant()->getDefaultSale()
            || $this->getProduct()->getParticipateSale();
        $saleDiscountType = !$this->getProductVariant()->getDefaultSale()
            ? $this->getProductVariant()->getDiscountType()
            : $this->getProduct()->getDiscountType();

        return $hasSpecificSale
                && $saleDiscountType === \XLite\Model\Product::SALE_DISCOUNT_TYPE_PRICE;
    }
}
