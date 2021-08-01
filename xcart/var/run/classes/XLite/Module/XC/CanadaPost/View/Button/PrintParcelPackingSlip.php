<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\Button;

/**
 * 'Print invoice' button widget
 */
class PrintParcelPackingSlip extends \XLite\View\Button\PrintPackingSlip
{
    /**
     * Widget params
     */
    const PARAM_PARCEL_ID = 'parcelId';

    /**
     * Get default label
     * todo: move translation here
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t('Print packing slip');
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
            static::PARAM_PARCEL_ID => new \XLite\Model\WidgetParam\TypeInt('Parcel ID', 0),
        );
    }

    /**
     * Return URL params to use with onclick event
     *
     * @return array
     */
    protected function getURLParams()
    {
        return [
            'url_params' => [
                'target'       => 'order',
                'order_number' => $this->getOrder()->getOrderNumber(),
                'mode'         => 'parcel_packing_slip',
                'parcel_id'    => $this->getParam(static::PARAM_PARCEL_ID),
            ],
        ];
    }
}
