<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\Button;

/**
 * Product selection in popup
 */
class PopupProductSelector extends \XLite\View\Button\PopupProductSelector
{
    const PARAM_COUPON_ID  = 'coupon_id';

    /**
     * Defines the target of the product selector
     * The main reason is to get the title for the selector from the controller
     *
     * @return string
     */
    protected function getSelectorTarget()
    {
        return 'coupon_product_selections';
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        $couponId = $this->getParam(static::PARAM_COUPON_ID);

        return array_merge(
            parent::prepareURLParams(),
            array(
                'coupon_id' => $couponId,
            )
        );
    }

    /**
     * Defines the class name of the widget which will display the product list dialog
     *
     * @return string
     */
    protected function getSelectorViewClass()
    {
        return '\XLite\Module\CDev\Coupons\View\ProductSelections';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_COUPON_ID  => new \XLite\Model\WidgetParam\TypeString('Coupon id', ''),
        );
    }
}
