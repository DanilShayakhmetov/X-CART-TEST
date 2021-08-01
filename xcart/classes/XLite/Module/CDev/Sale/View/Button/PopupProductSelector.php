<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View\Button;

/**
 * Product selection in popup
 */
class PopupProductSelector extends \XLite\View\Button\PopupProductSelector
{
    const PARAM_SALE_DISCOUNT_ID  = 'sale_discount_id';

    /**
     * Defines the target of the product selector
     * The main reason is to get the title for the selector from the controller
     *
     * @return string
     */
    protected function getSelectorTarget()
    {
        return 'sale_discount_product_selections';
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        $saleDiscountId = $this->getParam(static::PARAM_SALE_DISCOUNT_ID);

        return array_merge(
            parent::prepareURLParams(),
            array(
                'sale_discount_id' => $saleDiscountId,
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
        return '\XLite\Module\CDev\Sale\View\ProductSelections';
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
            static::PARAM_SALE_DISCOUNT_ID  => new \XLite\Model\WidgetParam\TypeString('Sale discount id', ''),
        );
    }
}
