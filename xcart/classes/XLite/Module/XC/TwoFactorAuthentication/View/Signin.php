<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\View;

class Signin extends \XLite\View\Signin implements \XLite\Base\IDecorator
{
    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        if (\XLite\Core\Config::getInstance()->XC->TwoFactorAuthentication->customer_interface) {
            $list[] = 'modules/XC/TwoFactorAuthentication/checkout/login.js';
        }

        return $list;
    }
}
