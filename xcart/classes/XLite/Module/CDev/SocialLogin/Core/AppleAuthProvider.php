<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SocialLogin\Core;

/**
 * Google auth provider
 */
class AppleAuthProvider extends AAuthProvider
{
    /**
     * Unique auth provider name
     */
    const PROVIDER_NAME = 'apple';

    /**
     * Url to which user will be redirected
     */
    const AUTH_REQUEST_URL = 'https://appleid.apple.com/auth/authorize';

    /**
     * Data to gain access to
     */
    const AUTH_REQUEST_SCOPE = 'name email';


    /**
     * Get authorization request url
     *
     * @param string $state State parameter to include in request
     * @param string $returnUrl
     *
     * @return string
     */
    public function getAuthRequestUrl($state, $returnUrl = null)
    {
        $url = static::AUTH_REQUEST_URL
            . '?client_id=' . $this->getClientId()
            . '&redirect_uri=' . urlencode($this->getRedirectUrl())
            . '&scope=' . static::getAuthRequestScope()
            . '&response_type=code id_token'
            . '&response_mode=form_post'
            . '&' . static::STATE_PARAM_NAME . '=' . urlencode($state);

        return $url;
    }

    /**
     * Process authorization grant and return array with profile data
     *
     * @return array Client information containing at least id and e-mail
     */
    public function processAuth()
    {
        $profile = [];

        $request = \XLite\Core\Request::getInstance();

        if (isset($request->id_token)) {
            list($header, $payload, $signature) = explode('.', $request->id_token);
            $data = json_decode(base64_decode($payload));
            $profile['id'] = $data->sub ?? null;
            $profile['email'] = $data->email ?? null;
        }

        if (isset($request->user)) {
            $user = json_decode($request->user);
            $profile['first_name'] = $user->name->firstName ?? null;
            $profile['last_name'] = $user->name->lastName ?? null;
        }

        return $profile;
    }

    /**
     * Get address from auth provider
     *
     * @param array $profileInfo Previous request result
     *
     * @return \XLite\Model\Address
     */
    public function processAddress($profileInfo)
    {
        $address = \XLite\Model\Address::createDefaultShippingAddress();

        $address->setIsShipping(true);
        $address->setIsBilling(true);
        $address->setIsWork(false);

        if (isset($profileInfo['first_name'])) {
            $address->setFirstname($profileInfo['first_name']);
        }

        if (isset($profileInfo['last_name'])) {
            $address->setLastname($profileInfo['last_name']);
        }

        return $address;
    }

    /**
     * Get OAuth 2.0 client ID
     *
     * @return string
     */
    protected function getClientId()
    {
        return \XLite\Core\Config::getInstance()->CDev->SocialLogin->apple_identifier;
    }

    /**
     * Check if auth provider has all options configured
     *
     * @return boolean
     */
    public function isConfigured()
    {
        return $this->getClientId();
    }

    /**
     * Get OAuth 2.0 client secret
     *
     * @return string
     */
    protected function getClientSecret()
    {
        return null;
    }
}
