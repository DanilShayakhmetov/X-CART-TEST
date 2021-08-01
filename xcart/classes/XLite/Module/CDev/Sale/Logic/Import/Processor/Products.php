<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Logic\Import\Processor;

/**
 * Products
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

        $columns['sale'] = [];
        $columns['saleDiscounts'] = [
            static::COLUMN_IS_MULTIPLE => true,
        ];

        return $columns;
    }

    // }}}

    // {{{ Verification

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + array(
                'PRODUCT-SALE-FMT' => 'Wrong sale format',
                'SALE-DISCOUNT-MISSING' => 'The "{{saleDiscount}}" discount does not exist',
            );
    }

    /**
     * Verify 'sale' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifySale($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsSale($value)) {
            $this->addWarning('PRODUCT-SALE-FMT', array('column' => $column, 'value' => $value));
        }
    }

    /**
     * Verify 'saleDiscounts' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     */
    protected function verifySaleDiscounts($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsNull($value)) {
            foreach ($value as $saleDiscount) {
                if (!$this->verifyValueAsEmpty($saleDiscount) && !$this->verifyValueAsSaleDiscount($saleDiscount)) {
                    $this->addWarning('SALE-DISCOUNT-MISSING', ['column' => $column, 'saleDiscount' => $saleDiscount]);
                }
            }
        }
    }

    /**
     * Verify value as correct sale value
     *
     * @param string $value  Value
     *
     * @return boolean
     */
    protected function verifyValueAsSale($value)
    {
        return preg_match('/^\d+\.?\d*(%)?$/', $value)
            && floatval($value) >= 0
            && (
                strpos($value, '%') === false
                || floatval($value) < 100
            );
    }

    /**
     * Verify value as sale discount
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsSaleDiscount($value)
    {
        return !is_null(\XLite\Core\Database::getRepo('XLite\Module\CDev\Sale\Model\SaleDiscount')->findOneByName($value));
    }


    // }}}

    // {{{ Import

    /**
     * Import 'sale' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importSaleColumn(\XLite\Model\Product $model, $value, array $column)
    {
        if ($this->verifyValueAsSale($value)) {
            $model->setParticipateSale(true);
            $model->setSalePriceValue(floatval($value));
            $model->setDiscountType(
                strpos($value, '%') > 0
                    ? \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT
                    : \XLite\Model\Product::SALE_DISCOUNT_TYPE_PRICE
            );

        } else {
            $model->setParticipateSale(false);
        }
    }

    /**
     * Import 'saleDiscounts' value
     *
     * @param \XLite\Model\Product $model Product
     * @param array $value Value
     * @param array $column Column info
     */
    protected function importSaleDiscountsColumn(\XLite\Model\Product $model, $value, array $column)
    {
        $saleDiscountIds = [];
        if ($value && is_array($value)) {
            foreach ($value as $discountName) {
                $saleDiscount = $this->normalizeValueAsSaleDiscount($discountName);

                if ($saleDiscount) {
                    $saleDiscountIds[] = $saleDiscount->getId();
                }
            }

            $model->replaceSpecificProductSaleDiscounts($saleDiscountIds);
        }
    }

    // }}}

    /**
     * Normalize value as sale discount
     *
     * @param mixed @value Value
     *
     * @return \XLite\Module\CDev\Sale\Model\SaleDiscount
     */
    protected function normalizeValueAsSaleDiscount($value)
    {
        $result = null;

        if ($value) {
            $result = $saleDiscount = \XLite\Core\Database::getRepo('XLite\Module\CDev\Sale\Model\SaleDiscount')
                ->findOneByName($value);
        }

        return $result;
    }
}
