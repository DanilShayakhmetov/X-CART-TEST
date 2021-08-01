<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Controller\Customer;

use XLite\Core\Auth;
use XLite\Module\XC\CrispWhiteSkin;

class ACustomer extends \XLite\Controller\Customer\ACustomer implements \XLite\Base\IDecorator
{
    /**
     * Get current selected country if available
     *
     * @return \XLite\Model\Country
     */
    public function getCurrentCountry()
    {
        if (CrispWhiteSkin\Main::isModuleEnabled('XC\MultiCurrency')) {
            return \XLite\Module\XC\MultiCurrency\Core\MultiCurrency::getInstance()->getSelectedCountry();
        } elseif (Auth::getInstance()->getProfile() && Auth::getInstance()->getProfile()->getShippingAddress()) {
            return Auth::getInstance()->getProfile()->getShippingAddress()->getCountry();
        } elseif (CrispWhiteSkin\Main::isModuleEnabled('XC\Geolocation')) {
            return \XLite\Model\Address::getDefaultFieldValue('country');
        }

        return null;
    }

    /**
     * Get current selected currency if available
     *
     * @return \XLite\Model\Currency
     */
    public function getCurrentCurrency()
    {
        $currency = null;

        if (CrispWhiteSkin\Main::isModuleEnabled('XC\MultiCurrency')) {
            $currency = \XLite\Module\XC\MultiCurrency\Core\MultiCurrency::getInstance()->getSelectedMultiCurrency();
        }

        return $currency;
    }

    /**
     * Return true if there are active currencies for currency selector
     *
     * @return boolean
     */
    public function isCurrencySelectorAvailable()
    {
        $result = false;

        if (CrispWhiteSkin\Main::isModuleEnabled('XC\MultiCurrency')) {
            $result = \XLite\Module\XC\MultiCurrency\Core\MultiCurrency::getInstance()->hasMultipleCurrencies();
        }

        return $result;
    }

    /**
     * Return profile email
     *
     * @return null|string
     */
    public function getProfileLogin()
    {
        return Auth::getInstance()->getProfile()
            ? Auth::getInstance()->getProfile()->getLogin()
            : null;
    }

    /**
     * Check if additional mobile breadcrumbs are shown
     *
     * @return boolean
     */
    public function isShowAdditionalMobileBreadcrumbs()
    {
        return false;
    }
}
