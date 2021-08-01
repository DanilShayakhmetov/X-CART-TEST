<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\ItemsList\Model;

/**
 * Coupon product selection items list
 */
class CouponProductSelection extends \XLite\View\ItemsList\Model\ProductSelection
{
    /**
     * Return wrapper form options
     *
     * @return string
     */
    protected function getFormOptions()
    {
        $options = parent::getFormOptions();

        $options['class'] = \XLite\Module\CDev\Coupons\View\Form\ItemsList\ProductSelection\Table::class;

        return $options;
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();
        $this->commonParams['coupon_id'] = \XLite\Core\Request::getInstance()->coupon_id;

        return $this->commonParams;
    }
}
