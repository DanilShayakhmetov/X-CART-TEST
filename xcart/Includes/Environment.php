<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes;


/**
 * Environment
 */
class Environment
{
    /**
     * @return bool
     */
    public static function isApache()
    {
        return !empty($_SERVER['SERVER_SOFTWARE']) && preg_match('/apache/i', $_SERVER['SERVER_SOFTWARE']);
    }

    /**
     * @return bool
     */
    public static function isLighttpd()
    {
        return !empty($_SERVER['SERVER_SOFTWARE']) && preg_match('/lighttpd/i', $_SERVER['SERVER_SOFTWARE']);
    }

    /**
     * @return bool
     */
    public static function isNginx()
    {
        return !empty($_SERVER['SERVER_SOFTWARE']) && preg_match('/nginx/i', $_SERVER['SERVER_SOFTWARE']);
    }
}