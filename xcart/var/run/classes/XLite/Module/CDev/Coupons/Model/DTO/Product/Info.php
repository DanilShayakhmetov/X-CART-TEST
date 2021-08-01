<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Model\DTO\Product;

/**
 * Product
 */
 class Info extends \XLite\Module\CDev\GoSocial\Model\DTO\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @param mixed|\XLite\Model\Product $object
     */
    protected function init($object)
    {
        parent::init($object);

        $assignedCoupons = [];
        foreach ($object->getCouponProducts() as $couponProduct) {
            $assignedCoupons[] = $couponProduct->getCoupon()->getId();
        }

        $this->prices_and_inventory->coupons = $assignedCoupons;
    }

    /**
     * @param \XLite\Model\Product $object
     * @param array|null           $rawData
     *
     * @return mixed
     */
    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        $this->assignProductSpecificCoupons($object);
    }

    protected function assignProductSpecificCoupons($object)
    {
        $couponIds = $this->prices_and_inventory->coupons;

        $object->replaceSpecificProductCoupons($couponIds);
    }
}
