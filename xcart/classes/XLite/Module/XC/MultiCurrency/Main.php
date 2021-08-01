<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        return \XLite\Core\Converter::buildURL('currencies');
    }

    /**
     * @param string $message
     * @param mixed  $data
     */
    public static function log($message, $data)
    {
        \XLite\Logger::logCustom('MultiCurrency',
            [
                'message' => $message,
                'data'    => $data,
            ]
        );
    }
}
