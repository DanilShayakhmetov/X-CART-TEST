<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Buffer;

use Psr\Log\LoggerInterface;
use XCart\Bus\Client\MarketplaceClient;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Marketplace"})
 */
class DataSet implements IBuffer
{
    /**
     * @var array
     */
    private $actions = [];

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var MarketplaceClient
     */
    private $client;

    /**
     * @param MarketplaceClient $client
     */
    public function __construct(
        MarketplaceClient $client
    ) {
        $this->client = $client;
    }

    /**
     * @param $actions
     *
     * @return static
     */
    public function add($actions)
    {
        $this->actions = array_unique(array_merge(
            $this->actions,
            (array) $actions
        ));

        return $this;
    }

    /**
     * @return static
     */
    public function clear()
    {
        $this->actions = [];
        $this->hash    = null;
        $this->data    = null;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return mixed
     */
    public function getByCriteria($type)
    {
        $this->loadBuffered();

        return $this->data[$type] ?? null;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        $this->loadBuffered();

        return $this->data;
    }

    private function loadBuffered(): void
    {
        $hash = $this->buildHash($this->actions);

        if ($this->actions && ($this->hash === null || $hash !== $this->hash)) {
            $this->data = $this->requestData();
            $this->hash = $hash;
        }
    }

    /**
     * @return array
     */
    private function requestData(): array
    {
        return $this->client->getDataSet($this->actions);
    }

    /**
     * @param array $actions
     *
     * @return string
     */
    private function buildHash($actions): string
    {
        asort($actions);

        return md5(implode('|', $actions));
    }
}
