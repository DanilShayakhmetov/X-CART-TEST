<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\PaypalCommercePlatform;

use PayPalCheckoutSdk\Core\PayPalEnvironment;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;

class PaypalClient
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var bool
     */
    protected $sandbox;

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param bool   $sandbox
     */
    public function __construct($clientId, $clientSecret, $sandbox = false)
    {
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
        $this->sandbox      = $sandbox;
    }

    /**
     * @see https://developer.paypal.com/docs/checkout/reference/server-integration/setup-sdk/
     *
     * @return PayPalHttpClient
     */
    public function getClient(): PayPalHttpClient
    {
        return new PayPalHttpClient($this->getEnvironment());
    }

    /**
     * @return PayPalEnvironment
     */
    protected function getEnvironment(): PayPalEnvironment
    {
        return $this->sandbox
            ? new SandboxEnvironment($this->clientId, $this->clientSecret)
            : new ProductionEnvironment($this->clientId, $this->clientSecret);
    }
}
