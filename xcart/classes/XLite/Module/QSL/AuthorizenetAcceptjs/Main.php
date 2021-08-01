<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\AuthorizenetAcceptjs;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Logging
     *
     * @param string  $message Message or data
     * @param boolean $force   Force write OPTIONAL
     */
    public static function log($message, $force = false)
    {
        if ($force || \Includes\Utils\ConfigParser::getOptions(['performance', 'developer_mode'])) {
            \XLite\Logger::getInstance()->logCustom('AuthorizenetAcceptjs', $message);
        }
    }
}
