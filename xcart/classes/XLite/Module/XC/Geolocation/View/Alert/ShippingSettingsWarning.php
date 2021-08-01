<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\View\Alert;

/**
 * Shipping settings warning
 */
class ShippingSettingsWarning extends \XLite\View\Alert\Warning
{
    protected function isVisible()
    {
        return \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('XC', 'Geolocation');
    }

    protected function getAlertContent()
    {
        return static::t(
            'Your store uses the addon Geolocation',
            ['geoip-settings-link' => \Includes\Utils\Module\Manager::getRegistry()->getModuleSettingsUrl('XC' ,'Geolocation')]
        );
    }

    protected function getClass()
    {
        return parent::getClass() . ' alert-no-margin';
    }
}