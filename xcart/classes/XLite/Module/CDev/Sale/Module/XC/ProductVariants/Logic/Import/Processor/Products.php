<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Module\XC\ProductVariants\Logic\Import\Processor;

/**
 * Products
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
abstract class Products extends \XLite\Logic\Import\Processor\Products implements \XLite\Base\IDecorator
{
    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns[static::VARIANT_PREFIX . 'Sale'] = [
            static::COLUMN_IS_MULTIROW => true,
        ];

        return $columns;
    }

    // }}}

    // {{{ Verification

    /**
     * Verify 'variantSale' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyVariantSale($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value)) {
            foreach ($value as $offset => $val) {
                if (!$this->verifyValueAsSale($val)) {
                    $this->addWarning(
                        'PRODUCT-SALE-FMT',
                        array('column' => $column, 'value' => $value),
                        $offset + 1 - $this->rowStartIndex
                    );
                }
            }
        }
    }

    // }}}

    // {{{ Import

    /**
     * Import 'variantSale' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importVariantSaleColumn(\XLite\Model\Product $model, $value, array $column)
    {
        foreach ($this->variants as $rowIndex => $variant) {
            $saleValue = $value[$rowIndex] ?? '';

            if (!$this->verifyValueAsSale($saleValue)) {
                $variant->setDefaultSale(true);
                $variant->setSalePriceValue(0);
            } else {
                $variant->setDefaultSale(false);
                $variant->setSalePriceValue(floatval($saleValue));
                $variant->setDiscountType(
                    strpos($saleValue, '%') > 0
                        ? \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT
                        : \XLite\Model\Product::SALE_DISCOUNT_TYPE_PRICE
                );
            }
        }
    }

    // }}}
}
