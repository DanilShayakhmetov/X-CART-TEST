<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\FormField\Select;

/**
 * Proof of age type selector
 */
class MoveItem extends \XLite\View\FormField\Select\Regular
{
    const PARAM_PARCEL_ID = 'parcelId';

    /**
     * Define widget parameters
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
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            ''     => static::t('Select parcel'),
        );
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        if (!$this->getParcel()) {
            return [];
        }

        /** @noinspection AdditionOperationOnArraysInspection */
        return parent::getOptions() + $this->getAllowedToMoveParcels($this->getParcel());
    }

    /**
     * Get parcel ID
     *
     * @return integer
     */
    protected function getParcelId()
    {
        return $this->getParam(static::PARAM_PARCEL_ID);
    }

    /**
     * Get products return
     *
     * @return \XLite\Module\XC\CanadaPost\Model\Order\Parcel
     */
    protected function getParcel()
    {
        return \XLite\Core\Database::getRepo('XLite\Module\XC\CanadaPost\Model\Order\Parcel')
            ->find($this->getParcelId());
    }

    /**
     * Get allowed parcels to move
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel object
     *
     * @return array
     */
    public function getAllowedToMoveParcels(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        $allowedParcels = array();

        foreach ($parcel->getOrder()->getCapostParcels() as $p) {

            if (
                $p->getId() !== $parcel->getId()
                && $p->isEditable()
            ) {
                $allowedParcels[$p->getId()] = static::t('Parcel') . ' #' . $p->getNumber();
            }
        }

        $allowedParcels['NEW'] = static::t('New parcel');

        return $allowedParcels;
    }
}
