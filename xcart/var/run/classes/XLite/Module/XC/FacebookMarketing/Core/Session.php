<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Core;

/**
 * Current session
 */
 class Session extends \XLite\Module\XC\Geolocation\Core\Session implements \XLite\Base\IDecorator
{
    const PIXEL_PARAM_LAST_INITIATED_CART = 'facebook_pixel_last_initiated_cart';

    /**
     * Set Facebook Pixel last initiated cart id and timestamp
     *
     * @param      $cartId
     * @param null $time
     */
    public function setPixelLastInitiatedCart($cartId, $time = null)
    {
        if ($time === null) {
            $time = \XLite\Core\Converter::getInstance()->time();
        }

        $this->{static::PIXEL_PARAM_LAST_INITIATED_CART} = $cartId . '|' . $time;
    }

    /**
     * Return last initiated cart id and timestamp
     *
     * @return array|null
     */
    public function getPixelLastInitiatedCart()
    {
        if ($this->{static::PIXEL_PARAM_LAST_INITIATED_CART}) {
            return explode('|', $this->{static::PIXEL_PARAM_LAST_INITIATED_CART});
        }

        return null;
    }
}