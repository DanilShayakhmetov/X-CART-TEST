<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UPS;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        return \XLite\Core\Converter::buildURL('ups');
    }

    /**
     * Perform some actions at startup
     *
     * @return string
     */
    public static function init()
    {
        parent::init();

        // Register UPS shipping processor
        \XLite\Model\Shipping::getInstance()->registerProcessor(
            '\XLite\Module\XC\UPS\Model\Shipping\Processor\UPS'
        );
    }

    /**
     * Return true if module should work in strict mode
     * (strict mode enables the logging of errors like 'The module is not configured')
     *
     * @return boolean
     */
    public static function isStrictMode()
    {
        return false;
    }

    /**
     * Log message
     *
     * @param mixed $message Message to log
     */
    public static function addLog($message)
    {
        \XLite\Logger::logCustom('ups', $message);
    }
}
