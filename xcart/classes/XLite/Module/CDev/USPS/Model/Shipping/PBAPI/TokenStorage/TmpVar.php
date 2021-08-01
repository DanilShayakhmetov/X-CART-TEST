<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Model\Shipping\PBAPI\TokenStorage;

use XLite\Module\CDev\USPS\Model\Shipping\PBAPI\ITokenStorage;

class TmpVar implements ITokenStorage
{
    const TMP_VAR_NAME = 'pb_usps_token';

    /**
     * @var int
     */
    protected $expiration = self::TOKEN_TTL;

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        \XLite\Core\TmpVars::getInstance()->{self::TMP_VAR_NAME} = [
            'token'      => $token,
            'expiration' => $this->expiration,
        ];
    }

    /**
     * @return string
     * @throws TokenExpiredException
     * @throws TokenStorageException
     */
    public function getToken()
    {
        $tokenData = \XLite\Core\TmpVars::getInstance()->{self::TMP_VAR_NAME};

        if (!isset($tokenData['token'])) {
            throw new TokenStorageException();
        }

        if (!isset($tokenData['expiration']) || \LC_START_TIME > $tokenData['expiration']) {
            throw new TokenExpiredException();
        }

        return $tokenData['token'];
    }

    /**
     * @param int $expiration
     */
    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;
    }
}
