<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication;

use XLite\Core\Config;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Method to initialize concrete module instance
     *
     * @return void
     */
    public static function init()
    {
        parent::init();

        static::registerLibAutoloader();
    }

    /**
     * Register lib autoloader
     *
     * @return void
     */
    protected static function registerLibAutoloader()
    {
        require_once(static::getLibDirectoryPath() . LC_DS . 'vendor' . LC_DS . 'autoload.php');
    }

    /**
     * Absolute path to libs
     *
     * @return string
     */
    protected static function getLibDirectoryPath()
    {
        return LC_DIR_MODULES . 'XC' . LC_DS . 'TwoFactorAuthentication' . LC_DS . 'lib';
    }
}
