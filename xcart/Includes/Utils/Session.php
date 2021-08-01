<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

/**
 * Current session
 */
class Session extends \Includes\Utils\AUtils
{
    /**
     * Admin cookie name
     */
    const ADMIN_COOKIE_NAME = 'xid_admin_logged';

    /**
     * Admin cookie value
     */
    const ADMIN_COOKIE_VALUE = 'admin_logged';

    /**
     * Set the admin cookie with the defined value
     *
     * @return void
     */
    public static function setAdminCookie()
    {
        static::setCookieWrapper(static::getAdminCookieName(), static::getAdminCookieValue());
    }

    /**
     * Clear the admin cookie
     *
     * @return void
     */
    public static function clearAdminCookie()
    {
        static::setCookieWrapper(static::getAdminCookieName(), false);
        static::setCookieWrapper('bus_token', false);
    }

    /**
     * Check if the admin cookie is set
     *
     * @return boolean
     */
    public static function issetAdminCookie()
    {
        return isset($_COOKIE[static::getAdminCookieName()])
            ? (static::getAdminCookieValue() === $_COOKIE[static::getAdminCookieName()])
            : false;
    }

    /**
     * Defines the admin cookie name
     *
     * @return string
     */
    protected static function getAdminCookieName()
    {
        return static::ADMIN_COOKIE_NAME;
    }

    /**
     * Defines the admin cookie value
     *
     * @return string
     */
    protected static function getAdminCookieValue()
    {
        return static::ADMIN_COOKIE_VALUE;
    }

    /**
     * Set cookie
     *
     * @param string $name  Name of cookie variable
     * @param string $value Value of cookie variable
     *
     * @return void
     */
    protected static function setCookieWrapper($name, $value)
    {
        if (!headers_sent()
            && 'cli' !== PHP_SAPI
        ) {
            \XLite\Core\Request::getInstance()->setCookie($name, $value);
        }
    }
}
