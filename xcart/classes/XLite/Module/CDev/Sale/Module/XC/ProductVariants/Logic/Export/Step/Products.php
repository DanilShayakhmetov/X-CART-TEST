<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Module\XC\ProductVariants\Logic\Export\Step;

/**
 * Products
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
abstract class Products extends \XLite\Logic\Export\Step\Products implements \XLite\Base\IDecorator
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        if ('none' !== $this->generator->getOptions()->attrs) {
            $columns += [
                static::VARIANT_PREFIX . 'Sale' => [static::COLUMN_MULTIPLE => true],
            ];
        }

        return $columns;
    }

    /**
     * Get column value for 'variantSale' column
     *
     * @param array $dataset Dataset
     * @param string $name Column name
     * @param integer $i Subcolumn index
     *
     * @return string
     */
    protected function getVariantSaleColumnValue(array $dataset, $name, $i)
    {
        $result = '';

        if (!empty($dataset['variant']) && !$this->getColumnValueByName($dataset['variant'], 'defaultSale')) {
            $result = $this->getColumnValueByName($dataset['variant'], 'salePriceValue');
            if (\XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT == $dataset['variant']->getDiscountType()) {
                $result .= '%';
            }
        }

        return $result;
    }
}
