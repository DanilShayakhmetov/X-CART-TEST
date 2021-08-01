<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\Module\XC\MailChimp\Logic\DataMapper;


/**
 * Coupon
 *
 * @Decorator\Depend("XC\MailChimp")
 */
 class Coupon extends \XLite\Module\CDev\Coupons\Module\XC\MailChimp\Logic\DataMapper\CouponAbstract implements \XLite\Base\IDecorator
{
    public static function getPromoRuleDataByCoupon(\XLite\Module\CDev\Coupons\Model\Coupon $coupon)
    {
        $data = parent::getPromoRuleDataByCoupon($coupon);

        if ($coupon->isFreeShipping()) {
            $data['amount'] = 0;
            $data['type'] = 'fixed';
            $data['target'] = 'shipping';
        }

        return $data;
    }
}