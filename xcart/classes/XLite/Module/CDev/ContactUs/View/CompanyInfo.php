<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ContactUs\View;


class CompanyInfo extends \XLite\View\AView
{
    use \XLite\Core\Cache\ExecuteCachedTrait;

    /**
     * @inheritdoc
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/ContactUs/contact_us/company_info.twig';
    }

    /**
     * @return boolean
     */
    public function hasLocation()
    {
        return (boolean)$this->getLocation();
    }

    /**
     * @return array
     */
    public function getLocation()
    {
        return $this->executeCachedRuntime(function () {
            $config = \XLite\Core\Config::getInstance()->Company;

            $parts = [
                'address' => $config->location_address,
                'city'    => $config->location_city,
            ];

            $hasStates = $config->locationCountry && $config->locationCountry->hasStates();

            if ($hasStates) {
                $locationState = \XLite\Core\Database::getRepo('XLite\Model\State')->find($config->location_state);
                $locationState = $locationState ? $locationState->getCode() : null;
            } else {
                $locationState = \XLite\Core\Database::getRepo('XLite\Model\State')->getOtherState($config->location_custom_state);
                $locationState = $locationState ? $locationState->getState() : null;
            }

            $parts['state'] = $locationState;
            $parts['zipcode'] = $config->location_zipcode;

            $parts['country'] = $config->location_country;
            if ($config->locationCountry) {
                $parts['country'] = $config->locationCountry->getCountry();
            }

            return array_filter($parts, function ($v) {
                return '' !== trim($v);
            });
        });
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return \XLite\Core\Config::getInstance()->CDev->ContactUs->showEmail
            ? \XLite\Core\Mailer::getSupportDepartmentMail()
            : '';
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return \XLite\Core\Config::getInstance()->Company->company_phone;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return \XLite\Core\Config::getInstance()->Company->company_fax;
    }

    /**
     * CSS class
     *   "separated" or "location"
     * It is going to be configurable sometimes
     *
     * @return string
     */
    public function getLocationMode()
    {
        return 'separated';
    }
}
