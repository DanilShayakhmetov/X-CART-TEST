<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Profile;


use XLite\Core\Converter;

class CreatedCustomer extends AProfile
{
    public static function getInterface()
    {
        return \XLite::CUSTOMER_INTERFACE;
    }

    public static function getDir()
    {
        return 'profile_created';
    }

    protected static function defineVariables()
    {
        return [
            'sign_in_url' => Converter::buildFullURL('login', '', [], \XLite::getCustomerScript())
            ] + parent::defineVariables();
    }

    public function __construct(\XLite\Model\Profile $profile, $password = null, $byCheckout = false)
    {
        parent::__construct($profile);
        $this->appendData([
            'password'   => $password,
            'byCheckout' => $byCheckout,
        ]);
    }
}