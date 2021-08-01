<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\Module\CDev\Coupons\Model;


/**
 * UsedCoupon
 *
 * @Decorator\Depend("CDev\Coupons")
 */
 class UsedCoupon extends \XLite\Module\CDev\Coupons\Model\UsedCouponAbstract implements \XLite\Base\IDecorator
{
    /**
     * Get coupon public name
     *
     * @return string
     */
    public function getPublicName()
    {
        if ($this->getType() && $this->getType() === \XLite\Module\CDev\Coupons\Model\Coupon::TYPE_FREESHIP) {
            return sprintf('%s (%s)', $this->getPublicCode(), static::t('Free shipping'));
        }

        return parent::getPublicName();
    }
}