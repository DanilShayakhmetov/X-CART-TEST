<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\View\Button;

/**
 * Connect
 */
class Connect extends \XLite\View\Button\Link
{
    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/Stripe/button.css';

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/Stripe/button.js';

        return $list;
    }

    /**
     * Get default label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Connect with Stripe';
    }

    /**
     * We make the full location path for the provided URL
     *
     * @return string
     */
    protected function getLocationURL()
    {
        $currency = \XLite::getInstance()->getCurrency()
            ? strtolower(\XLite::getInstance()->getCurrency()->getCode())
            : 'usd';
        $company = \XLite\Core\Config::getInstance()->Company;
        $oauth = \XLite\Module\XC\Stripe\Core\OAuth::getInstance();

        $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findOneBy(array('service_name' => 'Stripe'));

        return 'https://connect.stripe.com/oauth/authorize'
            . '?response_type=code'
            . '&client_id=' . $oauth->getClientIdLive()
            . '&scope=read_write'
            . '&state=' . $oauth->generateURLState()
            . '&stripe_landing=register'
            . '&stripe_user[email]=' . \XLite\Core\Auth::getInstance()->getProfile()->getLogin()
            . '&stripe_user[url]=' . \XLite::getInstance()->getShopUrl(\Xlite\Core\Converter::buildUrl())
            . '&stripe_user[country]=' . $company->location_country
            . '&stripe_user[street_address]=' . $company->location_address
            . '&stripe_user[city]=' . $company->location_city
            . '&stripe_user[zip]=' . $company->location_zipcode
            . '&stripe_user[business_name]=' . $company->company_name
            . '&stripe_user[currency]=' . $currency;
    }

    /**
     * Get default style
     *
     * @return string
     */
    protected function getDefaultStyle()
    {
        return trim(parent::getDefaultStyle() . ' stripe-connect always-enabled');
    }
}