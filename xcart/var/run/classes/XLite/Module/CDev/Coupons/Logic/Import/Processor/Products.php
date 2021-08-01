<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Logic\Import\Processor;

use XLite\Core\Database;

/**
 * Products
 */
abstract class Products extends \XLite\Module\CDev\Egoods\Logic\Import\Processor\Products implements \XLite\Base\IDecorator
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['couponCodes'] = [
            static::COLUMN_IS_MULTIPLE => true,
        ];

        return $columns;
    }

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + [
                'COUPON-CODE-MISSING' => 'The "{{code}}" coupon does not exist',
            ];
    }

    /**
     * Verify 'couponCodes' value
     *
     * @param mixed $value Value
     * @param array $column Column info
     */
    protected function verifyCouponCodes($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsNull($value)) {
            foreach ($value as $couponCode) {
                if (!$this->verifyValueAsEmpty($couponCode) && !$this->verifyValueAsCouponCode($couponCode)) {
                    $this->addWarning('COUPON-CODE-MISSING', ['column' => $column, 'code' => $couponCode]);
                }
            }
        }
    }

    /**
     * Verify value as coupon code
     *
     * @param mixed @value Value
     *
     * @return boolean
     */
    protected function verifyValueAsCouponCode($value)
    {
        return !is_null(\XLite\Core\Database::getRepo('XLite\Module\CDev\Coupons\Model\Coupon')->findOneByCode($value));
    }

    /**
     * Import 'couponCodes' value
     *
     * @param \XLite\Model\Product $model Product
     * @param array $value Value
     * @param array $column Column info
     */
    protected function importCouponCodesColumn(\XLite\Model\Product $model, $value, array $column)
    {
        if ($value && is_array($value)) {
            foreach ($model->getCouponProducts() as $couponProduct) {
                $couponCode = $couponProduct->getCoupon()->getCode();
                if (!in_array($couponCode, $value)) {
                    \XLite\Core\Database::getRepo('XLite\Module\CDev\Coupons\Model\CouponProduct')->delete($couponProduct, false);
                } else {
                    unset($value[array_search($couponCode, $value)]);
                }
            }

            foreach ($value as $couponCode) {
                $coupon = \XLite\Core\Database::getRepo('XLite\Module\CDev\Coupons\Model\Coupon')->findOneByCode($couponCode);

                if ($coupon && $coupon->getSpecificProducts()) {
                    $couponProduct = new \XLite\Module\CDev\Coupons\Model\CouponProduct();
                    $couponProduct->setProduct($model);
                    $couponProduct->setCoupon($coupon);

                    \XLite\Core\Database::getEM()->persist($couponProduct);
                }
            }
        }
    }
}
