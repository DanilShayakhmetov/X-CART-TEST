<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        return \XLite\Core\Converter::buildURL('capost');
    }

    /**
     * Perform some actions at startup
     *
     * @return string
     */
    public static function init()
    {
        parent::init();

        // Register CanadaPost shipping processor
        \XLite\Model\Shipping::getInstance()->registerProcessor(
            'XLite\Module\XC\CanadaPost\Model\Shipping\Processor\CanadaPost'
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
}
