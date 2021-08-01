<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Model\Shipping\PBAPI;

interface ITokenStorage
{
    const TOKEN_TTL =  36000; // 10 * 60 * 60

    /**
     * @param string $token
     */
    public function setToken($token);

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param integer $expiration
     */
    public function setExpiration($expiration);
}
