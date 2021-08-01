<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Geolocation\Logic\GeoInput;

/**
 * Ip address input type
 */
class IpAddress implements \XLite\Module\XC\Geolocation\Logic\IGeoInput
{
    /**
     * @var string internal IP address
     */
    protected $ip;

    public function __construct($ip = null)
    {
        $this->ip = $ip ?: $this->getRemoteIPAddress();
    }

    /**
     * Returns hash of geo data, is used as key in cache.
     *
     * @return string
     */
    public function getHash()
    {
        return md5(serialize($this));
    }

    /**
     * Returns scalar representation of internal geo data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->ip;
    }

    /**
     * Returns default IP address from SERVER array
     *
     * @return string
     */
    protected function getRemoteIPAddress()
    {
        $ip = '';

        if (\XLite\Core\Request::getInstance()->getClientIp() !== 'UNKNOWN') {
            $ip = \XLite\Core\Request::getInstance()->getClientIp();
        }

        return $ip;
    }
}