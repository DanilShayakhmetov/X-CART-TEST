<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use XCart\Bus\Query\Data\Buffer\DataSet;

abstract class AMarketplaceDataSetSource implements IDataSource
{
    /**
     * @var SetDataSource
     */
    private $setDataSource;

    /**
     * @var DataSet
     */
    private $dataSet;

    /**
     * @param SetDataSource $setDataSource
     * @param DataSet       $dataSet
     */
    public function __construct(
        SetDataSource $setDataSource,
        DataSet $dataSet
    ) {
        $this->setDataSource = $setDataSource;
        $this->dataSet       = $dataSet;
    }

    public function loadDeferred(): void
    {
        if ($this->isExpired()) {
            $this->dataSet->add($this->getRequest());
        }
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        $this->retrieveData();

        return $this->setDataSource->find($this->getRequest()) ?? [];
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function saveAll(array $data): bool
    {
        return $this->setDataSource->saveOne($data, $this->getRequest());
    }

    /**
     * @param mixed $value
     * @param null  $id
     *
     * @return bool
     */
    public function saveOne($value, $id = null): bool
    {
        $data = $this->getAll();
        $data[$id] = $value;

        return $this->saveAll($data);
    }

    /**
     * @param mixed $id
     *
     * @return bool
     */
    public function removeOne($id): bool
    {
        $data = $this->getAll();
        unset($data[$id]);

        return $this->saveAll($data);
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        return $this->setDataSource->removeOne($this->getRequest());
    }

    /**
     * @param mixed $id
     *
     * @return mixed|null
     */
    public function find($id)
    {
        $data = $this->getAll();

        return $data[$id] ?? null;
    }

    /**
     * @return string
     */
    abstract protected function getRequest(): string;

    /**
     * Lifetime in seconds
     *
     * @return int
     */

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

        return empty($expiration[$this->getRequest()]) || $expiration[$this->getRequest()] < time();
    }

    protected function retrieveData(): void
    {
        $data = $this->dataSet->getByCriteria($this->getRequest());

        if ($data) {
            $data = array_map([$this, 'normalize'], $data);
            $data = array_combine(
                array_map([$this, 'buildItemId'], $data),
                $data
            );

            $this->setDataSource->saveOne($data, $this->getRequest());

            $lifetime = $this->getLifetime();
            if ($lifetime > 0) {
                $expiration = $this->setDataSource->find('expiration') ?: [];

                $expiration[$this->getRequest()] = time() + $lifetime;

                $this->setDataSource->saveOne($expiration, 'expiration');
            }
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
     * @param array $item
     *
     * @return string
     */
    protected function buildItemId($item): string
    {
        asort($item);

        return md5(implode('|', array_map(function ($field) {
            return \is_array($field) ? $this->buildItemId($field) : $field;
        }, $item)));
    }
}
