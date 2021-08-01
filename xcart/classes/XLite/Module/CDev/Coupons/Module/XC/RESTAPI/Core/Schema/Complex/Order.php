<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Module\XC\RESTAPI\Core\Schema\Complex;

/**
 * Order schema
 *
 * @Decorator\Depend("XC\RESTAPI")
 */
class Order extends \XLite\Module\XC\RESTAPI\Core\Schema\Complex\Order implements \XLite\Base\IDecorator
{
    /**
     * Convert model
     *
     * @param \XLite\Model\AEntity  $model            Entity
     * @param boolean               $withAssociations Convert with associations
     *
     * @return array
     */
    public function convertModel(\XLite\Model\AEntity $model, $withAssociations)
    {
        $result = parent::convertModel($model, $withAssociations);

        $coupons = [];
        foreach ($model->getUsedCoupons() as $usedCoupon) {
            $coupons[] = $usedCoupon->getCode();
        }

        $result['coupon'] = $coupons;

        return $result;
    }
}
