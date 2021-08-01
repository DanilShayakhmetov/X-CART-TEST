<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\Button;

/**
 * Create shipment button widget
 */
class CreateShipment extends \XLite\View\Button\AButton
{
    /**
     * Widget params
     */
    const PARAM_PARCEL_ID = 'parcelId';
    
    /**
     * getJSFiles
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/CanadaPost/button/js/create_shipment.js';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list['js'][] = 'js/jquery.blockUI.js';
        $list['js'][] = 'js/core.popup.js';

        return $list;
    }

    /**
     * Get default button label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t('Create shipment');
    }

    /**
     * Return JS parameters
     *
     * @return array
     */
    protected function getJSParams()
    {
        return array(
            'parcel_id' => $this->getParam(static::PARAM_PARCEL_ID),
        );
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
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/CanadaPost/button/shipment_action.twig';
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' capost-button-create-shipment';
    }
}
