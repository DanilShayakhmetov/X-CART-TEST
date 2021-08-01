<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS;

use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\RequestFactory;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        return \XLite\Core\Converter::buildURL('usps');
    }

    /**
     * Perform some actions at startup
     *
     * @return string
     */
    public static function init()
    {
        parent::init();

        \XLite\Model\Shipping::getInstance()->registerProcessor(
            'XLite\Module\CDev\USPS\Model\Shipping\Processor\USPS'
        );

        \XLite\Model\Shipping::getInstance()->registerProcessor(
            'XLite\Module\CDev\USPS\Model\Shipping\Processor\PB'
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
     * @param \XLite\Core\ConfigCell|null $config
     *
     * @return RequestFactory
     */
    public static function getRequestFactory($config = null)
    {
        if (is_null($config)) {
            $config = \XLite\Core\Config::getInstance()->CDev->USPS;
        }

        return new RequestFactory(
            $config->pbSandbox
                ? RequestFactory::MODE_SANDBOX
                : RequestFactory::MODE_PRODUCTION
        );
    }

    /**
     * @param mixed $message
     */
    public static function log($message)
    {
        if (\XLite\Core\Config::getInstance()->CDev->USPS->debug_enabled) {
            \XLite\Logger::logCustom('usps', $message);
        }
    }
}
