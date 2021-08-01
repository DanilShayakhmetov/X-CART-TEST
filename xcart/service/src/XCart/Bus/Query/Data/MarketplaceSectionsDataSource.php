<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use Silex\Application;
use XCart\Bus\Client\MarketplaceClient;
use XCart\Bus\Domain\Storage\StorageInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class MarketplaceSectionsDataSource extends AMarketplaceCachedDataSource
{
    /**
     * @param Application       $app
     * @param MarketplaceClient $client
     * @param SetDataSource     $setDataSource
     * @param StorageInterface  $storage
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        MarketplaceClient $client,
        SetDataSource $setDataSource,
        StorageInterface $storage
    ) {
        return new static(
            $client,
            $setDataSource,
            $storage->build($app['config']['cache_dir'], 'busMarketplaceSectionsStorage')
        );
    }

    /**
     * @return array
     */
    protected function doRequest(): array
    {
        return $this->client->getSections();
    }
}