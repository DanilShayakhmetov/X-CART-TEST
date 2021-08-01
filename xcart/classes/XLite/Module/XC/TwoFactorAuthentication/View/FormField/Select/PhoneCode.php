<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\View\FormField\Select;

/**
 * Integer
 */
class PhoneCode extends \XLite\View\FormField\Select\Regular
{

    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return $this->getCountryCodesList();
    }

    /**
     * @return array
     */
    protected function getCountryCodesList()
    {
        $list = [];
        foreach (\XLite\Module\XC\TwoFactorAuthentication\Core\PhoneCountryCodes::getInstance()->getList() as $country_code => $phone_code) {
            $list[$phone_code] = $country_code . ' ' . $phone_code;
        }

        return $list;
    }
}
