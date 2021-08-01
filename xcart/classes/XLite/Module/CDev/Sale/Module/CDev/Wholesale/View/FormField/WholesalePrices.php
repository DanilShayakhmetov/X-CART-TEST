<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Module\CDev\Wholesale\View\FormField;

/**
 * Wholesale prices
 *
 * @Decorator\Depend({"XC\ProductVariants","CDev\Wholesale"})
 */
class WholesalePrices extends \XLite\Module\CDev\Wholesale\View\FormField\WholesalePrices implements \XLite\Base\IDecorator
{
    /**
     * @return bool
     */
    protected function isVariantOnAbsoluteSale()
    {
        $hasSpecificSale = !$this->getEntity()->getDefaultSale()
            || $this->getEntity()->getProduct()->getParticipateSale();
        $saleDiscountType = !$this->getEntity()->getDefaultSale()
            ? $this->getEntity()->getDiscountType()
            : $this->getEntity()->getProduct()->getDiscountType();

        return $hasSpecificSale
            && $saleDiscountType === \XLite\Model\Product::SALE_DISCOUNT_TYPE_PRICE;
    }

    /**
     * @return bool
     */
    protected function isWholesaleNotAllowed()
    {
        return parent::isWholesaleNotAllowed()
            || $this->isVariantOnAbsoluteSale();
    }

    /**
     * @return string
     */
    protected function getWholesaleNotAllowedMessage()
    {
        if ($this->isVariantOnAbsoluteSale()) {
            $salePrice = $this->getEntity()->getDefaultSale()
                ? $this->getEntity()->getProduct()->getSalePriceValue()
                : $this->getEntity()->getSalePriceValue();

            return static::t(
                'Wholesale prices for this product variant are disabled because its sale price is set as an absolute value (X). To enable wholesale prices, use the relative value in % for the Sale field.',
                ['price' => $this->formatPrice($salePrice)]
            );
        }

        return parent::getWholesaleNotAllowedMessage();
    }
}
