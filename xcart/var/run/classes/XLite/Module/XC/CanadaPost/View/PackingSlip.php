<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View;

/**
 * Invoice widget
 *
 * @ListChild (list="order.children", weight="30", zone="admin")
 */
class PackingSlip extends \XLite\View\PackingSlip
{
    /**
     * Widget parameter names
     */
    const PARAM_PARCEL = 'parcel';

    /**
     * Get order
     *
     * @return \XLite\Module\XC\CanadaPost\Model\Order\Parcel
     */
    public function getParcel()
    {
        return $this->getParam(self::PARAM_PARCEL);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_PARCEL => new \XLite\Model\WidgetParam\TypeObject(
                'Parcel',
                null,
                false,
                'XLite\Module\XC\CanadaPost\Model\Order\Parcel'
            ),
        );
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getParcel();
    }

    /**
     * Returns packing slip title
     *
     * @return string
     */
    protected function getPackingSlipParcelNo()
    {
        return $this->getParcel()->getNumber();
    }

    /**
     * Returns order items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    protected function getOrderItems()
    {
        return array_map(function($item) {
            return $item->getOrderItem();
        },$this->getParcel()->getItems()->toArray());
    }
}
