<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\Core;

class PhoneCountryCodes extends \XLite\Base\Singleton
{
    /**
     * @return void
     */
    protected function __construct()
    {
        require_once LC_DIR_MODULES . 'XC' . LC_DS . 'TwoFactorAuthentication' . LC_DS . 'lib' . LC_DS . 'vendor' . LC_DS . 'autoload.php';
    }

    /**
     * @return array
     */
    public static function getList()
    {
        static $res;

        if ($res) {
            return $res;
        }

        return $res = \megastruktur\PhoneCountryCodes::getCodesList();
    }
}
