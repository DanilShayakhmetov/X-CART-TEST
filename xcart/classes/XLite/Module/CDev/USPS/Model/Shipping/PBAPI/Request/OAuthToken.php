<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Model\Shipping\PBAPI\Request;

class OAuthToken extends Request
{
    /**
     * @param string $endpoint
     * @param string $clientId
     * @param string $secret
     */
    public function __construct($endpoint, $clientId, $secret)
    {
        parent::__construct(
            $endpoint . '/oauth/token',
            'POST',
            'grant_type=client_credentials',
            [
                'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $secret),
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ]
        );
    }
}
