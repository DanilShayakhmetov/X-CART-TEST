<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Logic\Export\Step;


 class Products extends \XLite\Module\CDev\Egoods\Logic\Export\Step\Products implements \XLite\Base\IDecorator
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array_merge(parent::defineColumns(), [
            'couponCodes'   => [],
        ]);
    }

    /**
     * Get 'couponCodes' column value
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getCouponCodesColumnValue(array $dataset, $name, $i)
    {
        $result = [];

        foreach ($dataset['model']->getCouponProducts() as $couponProduct) {
            $result[] = $couponProduct->getCoupon()->getCode();
        }

        return $result;
    }
}