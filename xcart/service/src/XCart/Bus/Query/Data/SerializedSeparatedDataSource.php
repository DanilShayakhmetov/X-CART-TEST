<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use XCart\Bus\Domain\Storage\StorageIndex;
use XCart\Bus\Domain\Storage\StorageInterface;

class SerializedSeparatedDataSource implements IDataSource
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $storagePath;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var StorageInterface[]
     */
    private $storageInstances;

    /**
     * @var StorageIndex[]
     */
    private $storageIdentifiers;

    /**
     * @var array
     */
    private $oldStorageData;

    /**
     * @var array
     */
    private $data;

    /**
     * @param string           $path
     * @param string           $name
     * @param StorageInterface $storage
     */
    public function __construct($path, $name, StorageInterface $storage)
    {
        $this->path        = $path;
        $this->storagePath = "$path/$name";
        $this->storage     = $storage->build($path, "{$name}Identifiers");
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        if ($this->data !== null) {
            return $this->data;
        }

        $this->data = [];

        $useOldStorage = $this->readFormOldStorage();

        foreach ($this->getStorageIdentifiers() as $indexData) {
            if ($this->checkItemTTL($indexData)) {
                $storageId = $indexData->getStorageId();

                if ($indexData->isOldStorage()) {
                    $this->data[$storageId] = $this->getOldStorageData($storageId);

                } elseif ($storageData = $this->getStorageInstance($storageId)->read()) {
                        $this->data[$storageId] = $storageData['data'];
                }
            }
        }

        if ($useOldStorage) {
            $this->updateStorage();
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
        $result     = true;
        $this->data = [];

        foreach ($data as $storageId => $value) {
            $result = $this->writeStorageValue($storageId, $value, false) && $result;
        }

        $result = $this->updateStorage() && $result;

        return $result;
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
        $this->getAll();

        $id = $id ?: $this->buildItemId($value);

        if ($this->shouldAddIdToItemOnSave()) {
            if (is_array($value)) {
                $value = array_replace($value, [$this->getIdName() => $id]);
            } elseif (is_object($value)) {
                $value->{$this->getIdName()} = $id;
            }
        }

        return $this->writeStorageValue($id, $value);
    }

    /**
     * Clears runtime cache
     */
    public function clearLocalCache(): void
    {
        $this->data               = null;
        $this->storageInstances   = null;
        $this->storageIdentifiers = null;
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
        if ($this->find($id)) {
            return $this->writeStorageValue($id, null);
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
     * @return int
     */
    protected function getItemTTL(): int
    {
        return 0;
    }

    /**
     * @param StorageIndex $index
     *
     * @return bool
     */
    protected function checkItemTTL(StorageIndex $index): bool
    {
        $ttl = $this->getItemTTL();

        return !$ttl || (time() - $index->getTimestamp()) < $ttl;
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

    /**
     * @return string
     */
    protected function getOldDataFileName(): string
    {
        return '';
    }

    /**
     * @param string $storageId
     * @param mixed  $value
     * @param bool   $updateStorage
     *
     * @return bool
     */
    protected function writeStorageValue(string $storageId, $value, $updateStorage = true): bool
    {
        if ($value !== null) {
            $this->data[$storageId] = $value;
        } else {
            unset($this->data[$storageId]);
        }

        $index = $this->getIndexData($storageId);

        if (!$index->isOldStorage()) {
            $result = $this->getStorageInstance($storageId)
                ->write($value === null ? null : [
                    'timestamp' => time(),
                    'data'      => $value,
                ]);
        } else {
            $result = true;
        }

        if ($updateStorage) {
            $result = $this->updateStorage() && $result;
        }

        return $result;
    }

    /**
     * @return StorageIndex[]
     */
    protected function getStorageIdentifiers(): array
    {
        if ($this->storageIdentifiers === null) {
            $this->storageIdentifiers = $this->storage->read();
        }

        return $this->storageIdentifiers;
    }

    /**
     * @param $storageId
     *
     * @return StorageIndex
     */
    protected function getIndexData($storageId): StorageIndex
    {
        $this->getStorageIdentifiers();

        if (!isset($this->storageIdentifiers[$storageId])) {
            $this->storageIdentifiers[$storageId] = StorageIndex::create($storageId);
        }

        return $this->storageIdentifiers[$storageId];
    }

    /**
     * @param $storageId
     *
     * @return StorageInterface
     */
    protected function getStorageInstance($storageId): StorageInterface
    {
        if (!isset($this->storageInstances[$storageId])) {
            $indexData = $this->getIndexData($storageId);

            $this->storageInstances[$storageId] = $this->storage->build("{$this->storagePath}/{$indexData->getDirectory()}", $storageId);
        }

        return $this->storageInstances[$storageId];
    }

    /**
     * @return bool
     */
    protected function updateStorage(): bool
    {
        $newIdentifiers = [];
        $oldDataStorage = [];

        foreach ($this->data as $storageId => $value) {
            $index = $this->getIndexData($storageId);

            $newIdentifiers[$storageId] = $index;

            if ($index->isOldStorage()) {
                $oldDataStorage[$storageId] = $value;
            }
        }

        $this->storageIdentifiers = $newIdentifiers;

        if (!empty($oldDataStorage)) {
            $this->writeOldDataStorage($oldDataStorage);
        }

        return $this->storage->write($newIdentifiers);
    }

    /**
     * @return StorageInterface
     */
    protected function getOldStorage(): StorageInterface
    {
        if (!isset($this->storageInstances['old_storage'])) {
            $this->storageInstances['old_storage'] = $this->storage->build($this->path, $this->getOldDataFileName());
        }

        return $this->storageInstances['old_storage'];
    }

    /**
     * @return bool
     */
    protected function checkOldStorage(): bool
    {
        if (!$this->getOldDataFileName()) {
            return false;
        }

        $path = "{$this->path}/{$this->getOldDataFileName()}.data";

        if (!file_exists($path)) {
            return false;
        }

        $timestamp = filemtime($path);

        return !($timestamp === false || time() - $timestamp > $this->getItemTTL());
    }

    /**
     * @param $storageId
     *
     * @return mixed
     */
    protected function getOldStorageData($storageId)
    {
        if ($this->oldStorageData === null) {
            $this->oldStorageData = $this->getOldStorage()->read();
        }

        return $this->oldStorageData[$storageId] ?? null;
    }

    /**
     * @return bool
     */
    protected function readFormOldStorage(): bool
    {
        if (!$this->checkOldStorage()) {
            return false;
        }

        $data = $this->getOldStorage()->read();

        foreach ($data as $storageId => $value) {
            $this->data[$storageId] = $value;

            $this->getIndexData($storageId)->setOldStorage(true);
        }

        return count($data) > 0;
    }

    /**
     * @param array $data
     */
    protected function writeOldDataStorage(array $data)
    {
        $path      = "{$this->path}/{$this->getOldDataFileName()}.data";
        $timestamp = filemtime($path);

        $this->getOldStorage()->write($data);

        touch($path, $timestamp);
    }
}