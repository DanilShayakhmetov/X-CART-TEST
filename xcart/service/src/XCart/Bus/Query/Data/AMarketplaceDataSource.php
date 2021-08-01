<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use XCart\Bus\Client\MarketplaceClient;

abstract class AMarketplaceDataSource implements IDataSource
{
    /**
     * @var array|null
     */
    private $data;

    /**
     * @var MarketplaceClient
     */
    protected $client;

    /**
     * @param MarketplaceClient $client
     */
    public function __construct(
        MarketplaceClient $client
    ) {
        $this->client = $client;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        if ($this->data === null) {
            $this->retrieveData();
        }

        return $this->data;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function saveAll(array $data): bool
    {
        $this->data = $data;

        return true;
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        $this->data = null;

        return true;
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function find($id)
    {
        $data = $this->getAll();

        return $data[$id] ?? null;
    }

    /**
     * @param mixed $value
     * @param mixed $id
     *
     * @return bool
     */
    public function saveOne($value, $id = null): bool
    {
        $this->data[$id] = $value;

        return true;
    }

    /**
     * @param mixed $id
     *
     * @return bool
     */
    public function removeOne($id): bool
    {
        unset($this->data[$id]);

        return !isset($this->data[$id]);
    }

    /**
     * @return mixed
     */
    abstract protected function doRequest();

    private function retrieveData(): void
    {
        $this->data = $this->doRequest();
    }
}
