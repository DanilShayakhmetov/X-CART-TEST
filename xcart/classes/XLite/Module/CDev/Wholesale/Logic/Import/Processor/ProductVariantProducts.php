<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Logic\Import\Processor;

use XLite\Module\CDev\Wholesale\Model\Base\AWholesalePrice;

/**
 * Products
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
abstract class ProductVariantProducts extends \XLite\Logic\Import\Processor\Products implements \XLite\Base\IDecorator
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

        $columns[static::VARIANT_PREFIX . 'WholesalePrices'] = [
            static::COLUMN_IS_MULTIPLE => true,
            static::COLUMN_IS_MULTIROW => true,
        ];

        return $columns;
    }

    // }}}

    // {{{ Verification

    /**
     * Verify 'wholesalePrices' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyVariantWholesalePrices($value, array $column)
    {
        foreach ($this->variants as $rowIndex => $variant) {
            if (isset($value[$rowIndex])) {
                $values = [];
                foreach ($value[$rowIndex] as $price) {
                    if (preg_match('/^(\d+)(-(\d+))?(\((.+)\))?=(\d+\.?\d*)(%?)$/iSs', $price, $m)) {
                        $data = [
                            'membership'         => $this->normalizeValueAsMembership($m[5]),
                            'price'              => $m[6],
                            'quantityRangeBegin' => $m[1],
                            'quantityRangeEnd'   => intval($m[3]),
                        ];
                        if (isset($m[7]) && '%' == $m[7]) {
                            $data['type'] = AWholesalePrice::WHOLESALE_TYPE_PERCENT;
                        } else {
                            $data['type'] = AWholesalePrice::WHOLESALE_TYPE_PRICE;
                        }

                        $callback = function ($tier) use ($data) {
                            return $data['quantityRangeBegin'] === $tier['quantityRangeBegin']
                                   && $data['membership'] === $tier['membership'];
                        };

                        if (array_filter($values, $callback)) {
                            $this->addError('WHOLESALE-DUPLICATE-ERR', ['column' => $column, 'value' => $data]);
                        } else {
                            $values[] = $data;
                        }
                    }
                }
            }
        }
    }

    // }}}

    // {{{ Import

    /**
     * Import 'variantWholesalePrices' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importVariantWholesalePricesColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        foreach ($this->variants as $rowIndex => $variant) {
            foreach (\XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\ProductVariantWholesalePrice')->findByProductVariant($variant) as $price) {
                \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\ProductVariantWholesalePrice')->delete($price);
            }
            if (isset($value[$rowIndex])) {
                foreach ($value[$rowIndex] as $price) {
                    if (preg_match('/^(\d+)(-(\d+))?(\((.+)\))?=(\d+\.?\d*)(%?)$/iSs', $price, $m)) {
                        $price = new \XLite\Module\CDev\Wholesale\Model\ProductVariantWholesalePrice();
                        $price->setMembership($this->normalizeValueAsMembership($m[5]));
                        $price->setProductVariant($variant);
                        $price->setPrice($m[6]);
                        $price->setQuantityRangeBegin($m[1]);
                        $price->setQuantityRangeEnd((int)$m[3]);

                        if (isset($m[7]) && '%' == $m[7]) {
                            $price->setType(AWholesalePrice::WHOLESALE_TYPE_PERCENT);
                        } else {
                            $price->setType(AWholesalePrice::WHOLESALE_TYPE_PRICE);
                        }

                        \XLite\Core\Database::getEM()->persist($price);
                    }
                }
            }
        }
    }

    // }}}
}
