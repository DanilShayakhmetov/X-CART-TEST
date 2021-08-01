<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\WizardStep;

use XLite\Module\XC\Onboarding\View\AWizardStep;

class BusinessInfo extends AWizardStep
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
    protected function getPhone()
    {
        return \XLite\Core\Config::getInstance()->Company->company_phone;
    }

    /**
     * @return string
     */
    protected function getSellExperience()
    {
        return \XLite\Core\Config::getInstance()->Company->sell_experience;
    }

    /**
     * @return string
     */
    protected function getBusinessCategory()
    {
        return \XLite\Core\Config::getInstance()->Company->business_category;
    }

    /**
     * @return string
     */
    protected function getBusinessRevenue()
    {
        return \XLite\Core\Config::getInstance()->Company->business_revenue;
    }
}