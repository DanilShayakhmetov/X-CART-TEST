<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Module\XC\OrdersImport\Logic\Import\Processor;

use \XLite\Model\Order;
use XLite\Module\CDev\Coupons\Model\UsedCoupon;

/**
 * Orders
 *
 * @Decorator\Depend("XC\OrdersImport")
 */
class Orders extends \XLite\Module\XC\OrdersImport\Logic\Import\Processor\Orders implements \XLite\Base\IDecorator
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['coupon'] = [
            static::COLUMN_IS_MULTICOLUMN  => true,
            static::COLUMN_IS_MULTIROW     => true,
            static::COLUMN_HEADER_DETECTOR => true,
            static::COLUMN_IS_IMPORT_EMPTY => true,
        ];

        return $columns;
    }

    /**
     * Detect shippingStatus header(s)
     *
     * @param array $column Column info
     * @param array $row    Header row
     *
     * @return array
     */
    protected function detectCouponHeader(array $column, array $row)
    {
        return $this->detectHeaderByPattern('coupon.+', $row);
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
                'COUPON-CODE-EMPTY' => 'Coupon code is empty',
                'COUPON-TYPE-FMT'   => 'Wrong coupon type format',
                'COUPON-AMOUNT-FMT' => 'Wrong coupon amount format',
            ];
    }

    /**
     * Verify 'orderIdentity' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     */
    protected function verifyCoupon($value, array $column)
    {
        $coupons = [];
        foreach ($value as $field => $values) {
            foreach ($values as $k => $v) {
                $coupons[$k][$field] = $v;
            }
        }

        foreach ($coupons as $coupon) {
            if (!empty($coupon['couponCode']) || !empty($coupon['couponType']) || !empty($coupon['couponAmount'])) {
                if (empty($coupon['couponCode'])) {
                    $this->addError('COUPON-CODE-EMPTY', ['column' => $column, 'value' => $coupon['couponCode']]);
                }

                if (strlen($coupon['couponType']) != 1) {
                    $this->addError('COUPON-TYPE-FMT', ['column' => $column, 'value' => $coupon['couponType']]);
                }

                if (!$this->verifyValueAsFloat($coupon['couponAmount'])) {
                    $this->addError('COUPON-AMOUNT-FMT', ['column' => $column, 'value' => $coupon['couponAmount']]);
                }
            }
        }
    }

    /**
     * Import 'paymentStatus' value
     *
     * @param \XLite\Model\Order $order  Order
     * @param array              $value  Value
     * @param array              $column Column info
     */
    protected function importCouponColumn(Order $order, $value, array $column)
    {
        $coupons = [];
        foreach ($value as $field => $values) {
            foreach ($values as $k => $v) {
                $coupons[$k][$field] = $v;
            }
        }

        foreach ($coupons as $coupon) {
            if (!empty($coupon['couponCode']) || !empty($coupon['couponType']) || !empty($coupon['couponAmount'])) {
                $originalCoupon = \XLite\Core\Database::getRepo('XLite\Module\CDev\Coupons\Model\Coupon')
                    ->findOneByCode($coupon['couponCode']);

                $usedCoupon = null;
                /** @var \XLite\Module\CDev\Coupons\Model\UsedCoupon $orderUsedCoupon */
                foreach ($order->getUsedCoupons() as $orderUsedCoupon) {
                    if ($orderUsedCoupon->getCode() === $coupon['couponCode']) {
                        $usedCoupon = $orderUsedCoupon;
                        break;
                    }
                }

                if (!$usedCoupon) {
                    $usedCoupon = new UsedCoupon();
                    $usedCoupon->setCode($coupon['couponCode']);
                    $usedCoupon->setOrder($order);

                    if ($originalCoupon) {
                        $usedCoupon->setCoupon($originalCoupon);
                    }

                    \XLite\Core\Database::getEM()->persist($usedCoupon);

                    $order->addUsedCoupons($usedCoupon);
                }

                $usedCoupon->setValue((float)$coupon['couponAmount']);
                $usedCoupon->setType($coupon['couponType']);
            }
        }
    }
}