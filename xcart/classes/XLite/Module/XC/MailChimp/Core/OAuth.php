<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core;


class OAuth
{
    protected $clientId;
    protected $clientSecret;
    protected $oauthProxyUrl;

    protected $client;

    const AUTH_ENDPOINT     = 'https://login.mailchimp.com/oauth2/authorize';
    const TOKEN_ENDPOINT    = 'https://login.mailchimp.com/oauth2/token';
    const METADATA_ENDPOINT = 'https://login.mailchimp.com/oauth2/metadata';

    const GRANT_TYPE        = 'authorization_code';

    /**
     * OAuth constructor.
     *
     * @param $clientId
     * @param $clientSecret
     * @param $oauthProxyUrl
     */
    public function __construct($clientId, $clientSecret, $oauthProxyUrl)
    {
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
        $this->oauthProxyUrl = $oauthProxyUrl;

        $this->client = new \OAuth2\Client($this->clientId, $this->clientSecret);
    }

    /**
     * @param $redirectUrl
     *
     * @return string
     */
    public function getAuthUrl($redirectUrl)
    {
        $realRedirectUrl = $this->wrapRedirectUrlWithProxy($redirectUrl);

        return $this->client->getAuthenticationUrl(static::AUTH_ENDPOINT, $realRedirectUrl);
    }

    /**
     * @param $code
     * @param $redirectUrl
     *
     * @return null
     */
    public function getToken($code, $redirectUrl)
    {
        $realRedirectUrl = $this->wrapRedirectUrlWithProxy($redirectUrl);

        $params = [
            'code'          => $code,
            'redirect_uri'  => $realRedirectUrl,
        ];

        $response = $this->client->getAccessToken(static::TOKEN_ENDPOINT, static::GRANT_TYPE, $params);

        return isset($response['result']['access_token'])
            ? $response['result']['access_token']
            : null;
    }

    /**
     * @param $redirectUrl
     *
     * @return string
     */
    protected function wrapRedirectUrlWithProxy($redirectUrl)
    {
        $proxyParams = [ 'redirecturl' => $redirectUrl ];
        $separator = false === strpos($this->oauthProxyUrl, '?') ? '?' : '&';

        return $this->oauthProxyUrl . $separator . http_build_query($proxyParams, null, '&');
    }

    /**
     * @param $token
     *
     * @return mixed
     * @throws \Exception
     */
    public function getTokenMetadata($token)
    {
        $request = new \XLite\Core\HTTP\Request(static::METADATA_ENDPOINT);
        $request->verb = 'GET';
        $request->setHeader('Authorization', 'OAuth ' . $token);

        $response = $request->sendRequest();

        if (!$response || !$response->body) {
            throw new \Exception('Token metadata unavailable');
        }

        return json_decode($response->body);
    }
}
