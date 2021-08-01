<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Core;

use XLite\Module\CDev\Sale\View\FormField\Select\CombineDiscounts;

/**
 * Class QuickData
 * @Decorator\Depend({"XC\ProductVariants","CDev\Sale"})
 * @Decorator\Before("CDev\VAT")
 */
class QuickDataVariants extends \XLite\Core\QuickData implements \XLite\Base\IDecorator
{
    protected $maxNonSaleVariantPrices = [];
    protected $maxSaleVariantPrices = [];

    protected $minNonSaleVariantPrices = [];
    protected $minSaleVariantPrices = [];

    /**
     * @param \XLite\Model\Product $product
     */
    protected function calculateVariantSalePrices(\XLite\Model\Product $product)
    {
        if (
            !array_key_exists($product->getProductId(), $this->maxNonSaleVariantPrices)
            || !array_key_exists($product->getProductId(), $this->minNonSaleVariantPrices)
            || !array_key_exists($product->getProductId(), $this->maxSaleVariantPrices)
            || !array_key_exists($product->getProductId(), $this->minSaleVariantPrices)
        ) {
            $this->maxNonSaleVariantPrices[$product->getProductId()] = null;
            $this->minNonSaleVariantPrices[$product->getProductId()] = null;
            $this->maxSaleVariantPrices[$product->getProductId()] = null;
            $this->minSaleVariantPrices[$product->getProductId()] = null;

            if (!$product->getParticipateSale() && $product->hasVariants()) {
                foreach ($product->getVariants() as $variant) {
                    $variantPrice = $variant->getQuickDataPrice();
                    if ($variant->getDefaultSale()) {
                        if (
                            is_null($this->maxNonSaleVariantPrices[$product->getProductId()])
                            || $this->maxNonSaleVariantPrices[$product->getProductId()] < $variantPrice
                        ) {
                            $this->maxNonSaleVariantPrices[$product->getProductId()] = $variantPrice;
                        }

                        if (
                            is_null($this->minNonSaleVariantPrices[$product->getProductId()])
                            || $this->minNonSaleVariantPrices[$product->getProductId()] > $variantPrice
                        ) {
                            $this->minNonSaleVariantPrices[$product->getProductId()] = $variantPrice;
                        }

                    } else {
                        if (
                            is_null($this->maxSaleVariantPrices[$product->getProductId()])
                            || $this->maxSaleVariantPrices[$product->getProductId()] < $variantPrice
                        ) {
                            $this->maxSaleVariantPrices[$product->getProductId()] = $variantPrice;
                        }

                        if (
                            is_null($this->minSaleVariantPrices[$product->getProductId()])
                            || $this->minSaleVariantPrices[$product->getProductId()] > $variantPrice
                        ) {
                            $this->minSaleVariantPrices[$product->getProductId()] = $variantPrice;
                        }
                    }
                }
            }
        }
    }

    /**
     * @param \XLite\Model\Product $product
     * @return mixed
     */
    protected function getMaxNonSaleVariantPrice(\XLite\Model\Product $product)
    {
        if (!array_key_exists($product->getProductId(), $this->maxNonSaleVariantPrices)) {
            $this->calculateVariantSalePrices($product);
        }

        return $this->maxNonSaleVariantPrices[$product->getProductId()];
    }

    /**
     * @param \XLite\Model\Product $product
     * @return mixed
     */
    protected function getMinNonSaleVariantPrice(\XLite\Model\Product $product)
    {
        if (!array_key_exists($product->getProductId(), $this->minNonSaleVariantPrices)) {
            $this->calculateVariantSalePrices($product);
        }

        return $this->minNonSaleVariantPrices[$product->getProductId()];
    }

    /**
     * @param \XLite\Model\Product $product
     * @return mixed
     */
    protected function getMaxSaleVariantPrice(\XLite\Model\Product $product)
    {
        if (!array_key_exists($product->getProductId(), $this->maxSaleVariantPrices)) {
            $this->calculateVariantSalePrices($product);
        }

        return $this->maxSaleVariantPrices[$product->getProductId()];
    }

    /**
     * @param \XLite\Model\Product $product
     * @return mixed
     */
    protected function getMinSaleVariantPrice(\XLite\Model\Product $product)
    {
        if (!array_key_exists($product->getProductId(), $this->minSaleVariantPrices)) {
            $this->calculateVariantSalePrices($product);
        }

        return $this->minSaleVariantPrices[$product->getProductId()];
    }

    /**
     * @param \XLite\Model\Product $product
     * @param $membership
     * @param $zone
     * @return float
     */
    protected function getQuickDataMinPrice(\XLite\Model\Product $product, $membership, $zone)
    {
        $quickDataMinPrice = parent::getQuickDataMinPrice($product, $membership, $zone);

        $minVariantPrice = $this->getMinNonSaleVariantPrice($product);
        if (!is_null($minVariantPrice)) {
            $saleDiscounts = \XLite\Core\Database::getRepo('XLite\Module\CDev\Sale\Model\SaleDiscount')
                ->findAllActiveForCalculate();


            switch (\XLite\Core\Config::getInstance()->CDev->Sale->way_to_combine_discounts) {
                case CombineDiscounts::TYPE_APPLY_MAX:
                    foreach ($saleDiscounts as $saleDiscount) {
                        if ($this->isSaleDiscountApplicable($saleDiscount, $product, $membership)) {
                            $minVariantPrice = $minVariantPrice * (1 - $saleDiscount->getValue() / 100);
                            break;
                        }
                    }
                    break;
                case CombineDiscounts::TYPE_APPLY_MIN:
                    foreach (array_reverse($saleDiscounts) as $saleDiscount) {
                        if ($this->isSaleDiscountApplicable($saleDiscount, $product, $membership)) {
                            $minVariantPrice = $minVariantPrice * (1 - $saleDiscount->getValue() / 100);
                            break;
                        }
                    }
                    break;
                case CombineDiscounts::TYPE_SUM_UP:
                    $percentSum = 0;
                    foreach ($saleDiscounts as $saleDiscount) {
                        if ($this->isSaleDiscountApplicable($saleDiscount, $product, $membership)) {
                            $percentSum += $saleDiscount->getValue();
                        }
                    }
                    $minVariantPrice = $minVariantPrice * (1 - min(100, $percentSum) / 100);
                    break;
            }

            $quickDataMinPrice = $minVariantPrice;
            if (!is_null($this->getMinSaleVariantPrice($product))) {
                $quickDataMinPrice = min($this->getMinSaleVariantPrice($product), $minVariantPrice);
            }
        }

        return $quickDataMinPrice;
    }

    /**
     * @param \XLite\Model\Product $product
     * @param $membership
     * @param $zone
     * @return float
     */
    protected function getQuickDataMaxPrice(\XLite\Model\Product $product, $membership, $zone)
    {
        $quickDataMaxPrice = parent::getQuickDataMaxPrice($product, $membership, $zone);

        $maxVariantPrice = $this->getMaxNonSaleVariantPrice($product);
        if (!is_null($maxVariantPrice)) {
            $saleDiscounts = \XLite\Core\Database::getRepo('XLite\Module\CDev\Sale\Model\SaleDiscount')
                ->findAllActiveForCalculate();


            switch (\XLite\Core\Config::getInstance()->CDev->Sale->way_to_combine_discounts) {
                case CombineDiscounts::TYPE_APPLY_MAX:
                    foreach ($saleDiscounts as $saleDiscount) {
                        if ($this->isSaleDiscountApplicable($saleDiscount, $product, $membership)) {
                            $maxVariantPrice = $maxVariantPrice * (1 - $saleDiscount->getValue() / 100);
                            break;
                        }
                    }
                    break;
                case CombineDiscounts::TYPE_APPLY_MIN:
                    foreach (array_reverse($saleDiscounts) as $saleDiscount) {
                        if ($this->isSaleDiscountApplicable($saleDiscount, $product, $membership)) {
                            $maxVariantPrice = $maxVariantPrice * (1 - $saleDiscount->getValue() / 100);
                            break;
                        }
                    }
                    break;
                case CombineDiscounts::TYPE_SUM_UP:
                    $percentSum = 0;
                    foreach ($saleDiscounts as $saleDiscount) {
                        if ($this->isSaleDiscountApplicable($saleDiscount, $product, $membership)) {
                            $percentSum += $saleDiscount->getValue();
                        }
                    }
                    $maxVariantPrice = $maxVariantPrice * (1 - min(100, $percentSum) / 100);
                    break;
            }

            $quickDataMaxPrice = $maxVariantPrice;
            if (!is_null($this->getMaxSaleVariantPrice($product))) {
                $quickDataMaxPrice = max($this->getMaxSaleVariantPrice($product), $maxVariantPrice);
            }
        }

        return $quickDataMaxPrice;
    }
}