<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Logic\Order\Modifier;

/**
 * Hack for normal merge Coupons and VolumeDiscounts surcharges(to not exceed subtotal)
 * @Decorator\Depend ("CDev\VolumeDiscounts")
 */
 class VolumeDiscountsDiscount extends \XLite\Module\CDev\Coupons\Logic\Order\Modifier\DiscountAbstract implements \XLite\Base\IDecorator
{
    /**
     * @return float
     */
    public function getDiscountBase()
    {
        $subtotal = $this->order->getSubtotal();
        $expectedClass = 'XLite\Module\CDev\VolumeDiscounts\Logic\Order\Modifier\Discount';

        foreach ($this->getOrder()->getSurcharges() as $surcharge) {
            if ($surcharge->getClass() === $expectedClass) {
                $subtotal += $surcharge->getValue();
                break;
            }
        }

        return $subtotal;
    }
}
