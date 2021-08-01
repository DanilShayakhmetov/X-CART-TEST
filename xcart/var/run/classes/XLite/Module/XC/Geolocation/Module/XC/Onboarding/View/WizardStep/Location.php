<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\Module\XC\Onboarding\View\WizardStep;


/**
 * Location
 *
 * @Decorator\Depend("XC\Onboarding")
 */
 class Location extends \XLite\Module\XC\Onboarding\View\WizardStep\LocationAbstract implements \XLite\Base\IDecorator
{
    /**
     * @return bool
     */
    protected function isUseGeolocation()
    {
        return !\XLite\Core\Config::getInstance()->XC->Onboarding->disable_geolocation;
    }

    protected function getCountry()
    {
        return $this->isUseGeolocation() && $this->getGeolocationCountryCode()
            ? $this->getGeolocationCountryCode()
            : parent::getCountry();
    }

    protected function getCurrency()
    {
        if ($this->isUseGeolocation()) {
            /** @var \XLite\Model\Country $country */
            $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($this->getCountry());

            if ($country && $country->getCurrency()) {
                return $country->getCurrency()->getCurrencyId();
            }
        }

        return parent::getCurrency();
    }

    /**
     * @return string
     */
    protected function getGeolocationCountryCode()
    {
        $location = \XLite\Module\XC\Geolocation\Logic\Geolocation::getInstance()->getLocation(
            new \XLite\Module\XC\Geolocation\Logic\GeoInput\IpAddress
        );

        return $location['country'] ?? '';
    }
}