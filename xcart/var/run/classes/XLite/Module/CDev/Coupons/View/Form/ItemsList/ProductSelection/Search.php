<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\Form\ItemsList\ProductSelection;

/**
 * Product selections list table form
 */
class Search extends \XLite\View\Form\ItemsList\ProductSelection\Search
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'coupon_product_selections';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();
        $list[\XLite\Module\CDev\Coupons\View\ItemsList\Model\CouponProduct::PARAM_COUPON_ID]
            = \XLite\Core\Request::getInstance()->coupon_id;

        return $list;
    }
}
