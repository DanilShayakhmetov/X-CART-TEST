<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use XCart\Bus\Domain\Storage\StorageInterface;

class SerializedDataSource implements IDataSource
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var array
     */
    private $data;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        if ($this->data === null) {
            $this->data = $this->storage->read();
        }

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

        return $this->storage->write($data);
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
     * @param mixed       $value
     * @param string|null $id
     *
     * @return bool
     */
    public function saveOne($value, $id = null): bool
    {
        $data = $this->getAll();

        $id = $id ?: $this->buildItemId($value);

        if ($this->shouldAddIdToItemOnSave()) {
            if (is_array($value)) {
                $value = array_replace($value, [$this->getIdName() => $id]);
            } elseif (is_object($value)) {
                $value->{$this->getIdName()} = $id;
            }
        }

        $data[$id] = $value;

        return $this->saveAll($data);
    }

    /**
     * Clears runtime cache
     */
    public function clearLocalCache(): void
    {
        $this->data = null;
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        $this->clearLocalCache();

        return $this->saveAll([]);
    }

    /**
     * @param mixed $id
     *
     * @return bool
     */
    public function removeOne($id): bool
    {
        $data = $this->getAll();

        if ($this->find($id)) {
            unset($data[$id]);
            $this->saveAll($data);

            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getIdName(): string
    {
        return 'id';
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    protected function buildItemId($item): string
    {
        return is_array($item)
            ? $item[$this->getIdName()]
            : $item->{$this->getIdName()};
    }

    /**
     * @return bool
     */
    protected function shouldAddIdToItemOnSave(): bool
    {
        return true;
    }
}
