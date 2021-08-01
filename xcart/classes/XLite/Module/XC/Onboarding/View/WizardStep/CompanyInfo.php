<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\WizardStep;

/**
 * CompanyInfo
 */
class CompanyInfo extends \XLite\Module\XC\Onboarding\View\AWizardStep
{
    /**
     * @return string
     */
    protected function getCompanyName()
    {
        return \XLite\Core\Config::getInstance()->Company->company_name;
    }

    /**
     * @return string
     */
    protected function getAddress()
    {
        return \XLite\Core\Config::getInstance()->Company->location_address;
    }

    /**
     * @return string
     */
    protected function getCountry()
    {
        return \XLite\Core\Config::getInstance()->Company->location_country;
    }

    /**
     * @return string
     */
    protected function getState()
    {
        return \XLite\Core\Config::getInstance()->Company->location_state;
    }

    /**
     * @return string
     */
    protected function getOtherState()
    {
        return \XLite\Core\Config::getInstance()->Company->location_custom_state;
    }

    /**
     * @return string
     */
    protected function getPhone()
    {
        return \XLite\Core\Config::getInstance()->Company->company_phone;
    }

    /**
     * @return string
     */
    protected function getCity()
    {
        return \XLite\Core\Config::getInstance()->Company->location_city;
    }

    /**
     * @return string
     */
    protected function getZipCode()
    {
        return \XLite\Core\Config::getInstance()->Company->location_zipcode;
    }
}