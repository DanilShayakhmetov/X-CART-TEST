<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\WizardStep;

/**
 * Location
 */
abstract class LocationAbstract extends \XLite\Module\XC\Onboarding\View\AWizardStep
{
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            ['url' => 'https://www.gstatic.com/charts/loader.js'],
            'modules/XC/Onboarding/wizard_steps/location/map.js',
        ]);
    }

    /**
     * @return string
     */
    protected function getCountry()
    {
        return \XLite\Core\Config::getInstance()->Company->location_country;
    }

    /**
     * @return int
     */
    protected function getCurrency()
    {
        return \XLite\Core\Config::getInstance()->General->shop_currency;
    }

    /**
     * @return string
     */
    protected function getWeightUnit()
    {
        return \XLite\Core\Config::getInstance()->Units->weight_unit;
    }

    /**
     * @return string
     */
    protected function getMoreSettingsLocation()
    {
        return $this->buildURL('units_formats');
    }
}