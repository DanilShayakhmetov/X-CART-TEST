<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\PaypalCommercePlatform;

use PEAR2\HTTP\Request\Response;
use XLite\Core\HTTP\Request;
use XLite\Module\CDev\Paypal\Main as PaypalMain;

class AccessToken
{
    const SECURITY_HEADER = 'XCARTCDEVPAYPAL';

    /**
     * @var bool
     */
    protected $sandbox;

    /**
     * @var array
     */
    protected $accessTokenData;

    /**
     * @param bool $sandbox
     */
    public function __construct($sandbox)
    {
        $this->sandbox = $sandbox;
    }

    /**
     * @param string $message
     * @param mixed  $data
     */
    protected static function addLog($message = null, $data = null): void
    {
        PaypalMain::addLog('PaypalCommercePlatform Onboarding AccessToken:' . $message, $data);
    }

    /**
     * @return array {
     *      scope: string,
     *      access_token: string,
     *      token_type: string,
     *      app_id: string,
     *      expires_in: int,
     *      nonce: string,
     *      expiration: int,
     *      partner_id: string,
     * }
     */
    public function getAccessTokenData()
    {
        if ($this->accessTokenData === null) {
            $accessTokenData = \XLite\Core\Cache\ExecuteCached::getCache(['\XLite\Module\CDev\Paypal\Core\PaypalCommercePlatform\AccessToken::getAccessTokenData']);
            if (empty($accessTokenData)) {
                $accessTokenData = $this->retrieveAccessTokenData();

                if ($accessTokenData) {
                    \XLite\Core\Cache\ExecuteCached::setCache(
                        ['\XLite\Module\CDev\Paypal\Core\PaypalCommercePlatform\AccessToken::getAccessTokenData'],
                        $accessTokenData,
                        3600
                    );
                }
            }

            $this->accessTokenData = $accessTokenData;
        }

        return $this->accessTokenData;
    }

    /**
     * @return array
     */
    protected function retrieveAccessTokenData(): array
    {
        $result  = [];
        $request = new Request($this->getAccessTokenUrl());
        $request->setHeader(self::SECURITY_HEADER, hash('sha256', self::SECURITY_HEADER));

        $request->verb = 'post';

        static::addLog('Retrieve access token');

        $response = $request->sendRequest();

        if ($response instanceof Response && 200 == $response->code && !empty($response->body)) {
            $result = @json_decode($response->body, true);
        }

        if ($result) {
            static::addLog('Access token recieved', $result);
        }

        return is_array($result) ? $result : [];
    }

    /**
     * @return string
     */
    protected function getAccessTokenUrl(): string
    {
        return $this->isSandbox()
            ? 'https://mc-end-auth.qtmsoft.com/paypal-access-token.php?sandbox'
            : 'https://mc-end-auth.qtmsoft.com/paypal-access-token.php';
    }

    /**
     * @return bool
     */
    protected function isSandbox(): bool
    {
        return $this->sandbox;
    }
}
