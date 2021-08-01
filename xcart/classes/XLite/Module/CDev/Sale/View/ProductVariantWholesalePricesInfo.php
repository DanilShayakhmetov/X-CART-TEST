<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View;

/**
 * Wholesale prices info box
 * @ListChild (list="variant_wholesale_prices.info_message", zone="admin", weight="100")
 */
class ProductVariantWholesalePricesInfo extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Sale/wholesale_prices_info/body.twig';
    }

    protected function getInfoMessage()
    {
        $infoMessage = '';

        if (
            !$this->getProduct()->getParticipateSale()
            && !empty($this->getProduct()->getApplicableSaleDiscounts())
        ) {
            $saleDiscounts = $this->getProduct()->getApplicableSaleDiscounts();
            $links = [];
            /** @var \XLite\Module\CDev\Sale\Model\SaleDiscount $discount */
            foreach ($saleDiscounts as $discount) {
                $links[] = $this->getSaleDiscountEditLink($discount)
                    ? '<a href="' . $this->getSaleDiscountEditLink($discount) . '">' . $discount->getName() . '</a>'
                    : $discount->getName();
            }

            $infoMessage = static::t('The following sale discounts apply to this product: X', ['sales' => implode(', ', $links)]);
        }

        return $infoMessage;
    }

    protected function getWarningMessage()
    {
        $warningMessage = '';

        $hasSpecificSale = !$this->getProductVariant()->getDefaultSale()
            || $this->getProduct()->getParticipateSale();
        $saleDiscountType = !$this->getProductVariant()->getDefaultSale()
            ? $this->getProductVariant()->getDiscountType()
            : $this->getProduct()->getDiscountType();

        if (
            $hasSpecificSale
            && $saleDiscountType === \XLite\Model\Product::SALE_DISCOUNT_TYPE_PRICE
        ) {
            $salePrice = $this->getProductVariant()->getDefaultSale()
                ? $this->getProduct()->getSalePriceValue()
                : $this->getProductVariant()->getSalePriceValue();

            $warningMessage = static::t(
                'Wholesale prices for this product variant are disabled because its sale price is set as an absolute value (X). To enable wholesale prices, use the relative value in % for the Sale field.',
                ['price' => $this->formatPrice($salePrice)]
            );
        }

        return $warningMessage;
    }
}
