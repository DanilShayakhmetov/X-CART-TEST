<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\IntegrityCheck;

use XCart\Bus\Client\MarketplaceClient;
use XCart\Bus\Domain\Module;
use XCart\Bus\Exception\Rebuild\KnownHashesException;
use XCart\Bus\Query\Data\KnownHashesCacheDataSource;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class KnownHashesRetriever extends CachedRetriever
{
    /**
     * @var MarketplaceClient
     */
    private $marketplaceClient;

    /**
     * @var KnownHashesCacheDataSource
     */
    private $hashesCacheDataSource;

    /**
     * @param MarketplaceClient     $marketplaceClient
     * @param KnownHashesCacheDataSource $hashesCacheDataSource
     */
    public function __construct(
        MarketplaceClient $marketplaceClient,
        KnownHashesCacheDataSource $hashesCacheDataSource
    ) {
        $this->marketplaceClient = $marketplaceClient;
        $this->hashesCacheDataSource = $hashesCacheDataSource;
    }

    /**
     * @param Module $module
     *
     * @return array
     * @throws KnownHashesException
     */
    public function getHashes($module): array
    {
        $id = md5($module->id . '|' . $module->installedVersion);

        return $this->retrieveCached(
            $id,
            function () use ($module) {
                $hashes = $this->marketplaceClient->getHashes(
                    $module->id,
                    $module->installedVersion
                );

                $lastError = $this->marketplaceClient->getLastError();
                if ($lastError) {
                    throw new KnownHashesException($lastError->getCode(), $lastError->getMessage());
                }

                return $hashes;
            }
        );
    }

    /**
     * @return mixed
     */
    protected function getCacheDataSource()
    {
        return $this->hashesCacheDataSource;
    }
}
