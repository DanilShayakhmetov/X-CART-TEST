<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Module\XC\MailChimp\Logic\DataMapper;


use XLite\Model\Category;
use XLite\Model\Membership;
use XLite\Model\ProductClass;
use XLite\Module\CDev\Coupons\Module\XC\MailChimp\Core\Exception\CouponDoesNotMatch;
use XLite\Module\XC\MailChimp\View\FormField\Select\SendCoupons;

class Coupon
{
    /**
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $coupon
     *
     * @return string
     * @throws CouponDoesNotMatch
     */
    public static function getDescriptionByCoupon(\XLite\Module\CDev\Coupons\Model\Coupon $coupon)
    {
        $result = '';

        if (strlen($coupon->getComment()) > 0) {
            $result = $coupon->getComment();
        }

        $categories = array_map(function ($category) {
            /* @var Category $category */
            return $category->getName();
        }, $coupon->getCategories()->toArray());

        $productClasses = array_map(function ($productClass) {
            /* @var ProductClass $productClass */
            return $productClass->getName();
        }, $coupon->getProductClasses()->toArray());

        $memberships = array_map(function ($membership) {
            /* @var Membership $membership */
            return $membership->getName();
        }, $coupon->getMemberships()->toArray());

        $additional = array_filter([
            'Subtotal range (begin)'                       => $coupon->getTotalRangeBegin() ?: null,
            'Subtotal range (end)'                         => $coupon->getTotalRangeEnd() ?: null,
            'Categories'                                   => empty($categories) ? null : implode(',', $categories),
            'Product classes'                              => empty($productClasses) ? null : implode(',', $productClasses),
            'Memberships'                                  => empty($memberships) ? null : implode(',', $memberships),
            'Limit the number of uses'                     => $coupon->getUsesLimit() ?: null,
            'Limit the number of uses per user'            => $coupon->getUsesLimitPerUser() ?: null,
        ]);

        if (!empty($additional)) {
            if(\XLite\Core\Config::getInstance()->XC->MailChimp->send_coupons === SendCoupons::SEND_MATCH) {
                throw new CouponDoesNotMatch();
            }

            $_additional = [];
            foreach ($additional as $k => $v) {
                $_additional[] = $k . (is_bool($v) ? '' : ':' . $v);
            }

            $result .= "(Additional rules: " . implode('; ', $_additional) . ")";
        }

        return mb_strlen($result) > 255
            ? mb_substr($result, 0, 252) . "..."
            : $result;
    }

    /**
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $coupon
     *
     * @return array
     * @throws CouponDoesNotMatch
     */
    public static function getPromoRuleDataByCoupon(\XLite\Module\CDev\Coupons\Model\Coupon $coupon)
    {
        return [
            'id'          => strval($coupon->getId()),
            'description' => static::getDescriptionByCoupon($coupon),
            'starts_at'   => $coupon->getDateRangeBegin()
                ? date('c', $coupon->getDateRangeBegin())
                : date('c', 0),
            'ends_at'     => $coupon->getDateRangeEnd()
                ? date('c', $coupon->getDateRangeEnd())
                : date('c', 99999999999),
            'amount'      => $coupon->getType() === $coupon::TYPE_PERCENT
                ? $coupon->getValue() / 100
                : $coupon->getValue(),
            'type'        => $coupon->getType() === $coupon::TYPE_PERCENT
                ? 'percentage'
                : 'fixed',
            'target'      => 'total',
            'enabled'     => (boolean)$coupon->getEnabled(),
        ];
    }

    public static function getPromoCodeDataByCoupon(\XLite\Module\CDev\Coupons\Model\Coupon $coupon)
    {
        return [
            'id'             => strval($coupon->getId()),
            'code'           => mb_substr($coupon->getCode(), 0, 50),
            'redemption_url' => mb_substr(\XLite::getInstance()->getShopURL(), 0, 2000),
            'usage_count'    => $coupon->getUsedCoupons()
                ? $coupon->getUsedCoupons()->count()
                : 0,
            'enabled'        => (boolean)$coupon->getEnabled(),
        ];
    }
}