<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\Core;

/**
 * Request
 */
class Request extends \XLite\Core\Request implements \XLite\Base\IDecorator
{
    /**
     * Set cookie (The method is needed if cookies must be available for js)
     *
     * @param string  $name  Name
     * @param string  $value Value
     * @param integer $ttl   TTL OPTIONAL
     *
     */
    public function setJsCookie($name, $value, $ttl = 0)
    {
        $this->_setcookie(
            $name,
            $value,
            $ttl != 0 ? \XLite\Core\Converter::time() + $ttl : 0,
            $this->getCookiePath(false),
            null,
            $this->getCookieSecure(),
            false
        );
    }
}