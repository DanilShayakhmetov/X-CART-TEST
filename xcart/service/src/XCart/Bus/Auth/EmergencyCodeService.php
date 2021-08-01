<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Auth;

use Silex\Application;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class EmergencyCodeService
{
    /**
     * @var string
     */
    private $authCode;

    /**
     * @var string
     */
    private $serviceCode;

    /**
     * @param Application $app
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(Application $app)
    {
        return new self(
            $app['config']['authcode_reference'],
            $app['config']['service_authcode_reference']
        );
    }

    /**
     * @param string $authCode
     * @param string $serviceCode
     */
    public function __construct($authCode, $serviceCode)
    {
        $this->authCode    = $authCode;
        $this->serviceCode = $serviceCode;
    }

    /**
     * @param string $authCode
     *
     * @return bool
     */
    public function checkAuthCode($authCode): bool
    {
        return $authCode === $this->authCode;
    }

    /**
     * @param string $serviceCode
     *
     * @return bool
     */
    public function checkServiceCode($serviceCode): bool
    {
        return $serviceCode === $this->serviceCode;
    }
}
