<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Client;

use Exception;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class LicenseClient
{
    public const FREE_LICENSE = 'XC5-FREE-LICENSE';

    /**
     * @var MarketplaceClient
     */
    private $marketplaceClient;

    /**
     * @param MarketplaceClient $marketplaceClient
     */
    public function __construct(MarketplaceClient $marketplaceClient)
    {
        $this->marketplaceClient = $marketplaceClient;
    }

    /**
     * @param string $key
     * @param int    $wave
     *
     * @return array|null
     * @throws Exception
     */
    public function registerLicenseKey($key, $wave = null): ?array
    {
        $result = $this->marketplaceClient->registerLicenseKey($key, $wave);

        return $result[$key] ?? null;
    }

    /**
     * @param string|string[] $key
     *
     * @return array|array[]
     */
    public function getLicenseInfo($key): array
    {
        $result = $this->marketplaceClient->getLicenseInfo($key);

        return is_array($key) ? $result[$key] : $result;
    }

    /**
     * @return array
     */
    public function getFreeLicenseInfo(): array
    {
        $result = $this->marketplaceClient->getLicenseInfo(self::FREE_LICENSE);

        return $result[self::FREE_LICENSE][0]['keyData'] ?? [];
    }
}
