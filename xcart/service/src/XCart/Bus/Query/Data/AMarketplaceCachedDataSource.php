<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use XCart\Bus\Client\MarketplaceClient;
use XCart\Bus\Domain\Storage\StorageInterface;

abstract class AMarketplaceCachedDataSource extends SerializedDataSource
{
    /**
     * @var \XCart\Bus\Client\MarketplaceClient
     */
    protected $client;

    /**
     * @var SetDataSource
     */
    private $setDataSource;

    /**
     * @param MarketplaceClient $client
     * @param SetDataSource     $setDataSource
     * @param StorageInterface  $storage
     */
    public function __construct(
        MarketplaceClient $client,
        SetDataSource $setDataSource,
        StorageInterface $storage
    ) {
        $this->client        = $client;
        $this->setDataSource = $setDataSource;

        parent::__construct($storage);
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        if ($this->isExpired()) {
            $this->retrieveData();
        }

        return parent::getAll();
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        $expiration = $this->setDataSource->find('expiration') ?: [];

        $expiration[static::class] = null;

        $this->setDataSource->saveOne($expiration, 'expiration');

        return parent::clear();
    }

    /**
     * @return mixed
     */
    abstract protected function doRequest();

    /**
     * Lifetime in seconds
     *
     * @return int
     */
    protected function getLifetime(): int
    {
        return 86400;
    }

    /**
     * @return bool
     */
    protected function isExpired(): bool
    {
        $expiration = $this->setDataSource->find('expiration');

        return empty($expiration[static::class]) || $expiration[static::class] < time();
    }

    protected function retrieveData(): void
    {
        $data = $this->doRequest();

        if ($data) {
            $this->saveAll($data);

            $expiration = $this->setDataSource->find('expiration') ?: [];

            $expiration[static::class] = time() + $this->getLifetime();

            $this->setDataSource->saveOne($expiration, 'expiration');
        }
    }

    /**
     * @param $datum
     *
     * @return mixed
     */
    protected function normalize($datum)
    {
        return $datum;
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    protected function buildItemId($item): string
    {
        asort($item);

        return md5(implode('|', array_map(function ($e) {
            return \is_array($e) ? $this->buildItemId($e) : $e;
        }, $item)));
    }
}
