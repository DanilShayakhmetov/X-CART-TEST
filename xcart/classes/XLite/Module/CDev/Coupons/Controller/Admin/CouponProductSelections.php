<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Controller\Admin;

/**
 * Coupon products
 */
class CouponProductSelections extends \XLite\Controller\Admin\ProductSelections
{
    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage coupons');
    }

    /**
     * Check if the product id which will be displayed as "Already added"
     *
     * @param integer $productId Product ID
     *
     * @return bool
     */
    public function isExcludedProductId($productId)
    {
        $couponProduct = [
            'coupon'  => \XLite\Core\Request::getInstance()->coupon_id,
            'product' => $productId,
        ];

        return (bool)\XLite\Core\Database::getRepo('XLite\Module\CDev\Coupons\Model\CouponProduct')
                ->findOneBy($couponProduct);
    }

    /**
     * @return \XLite\Module\CDev\Coupons\Model\Coupon|null
     */
    public function getCoupon()
    {
        $couponId = \XLite\Core\Request::getInstance()->coupon_id;

        return $this->executeCachedRuntime(function() use ($couponId) {
            return \XLite\Core\Database::getRepo('XLite\Module\CDev\Coupons\Model\Coupon')
                ->find($couponId);
        }, ['getCoupon', $couponId]);
    }
}
