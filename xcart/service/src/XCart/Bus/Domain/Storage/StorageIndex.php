<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Domain\Storage;

class StorageIndex
{
    /**
     * @var string
     */
    protected $storageId;

    /**
     * @var int
     */
    protected $timestamp;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var bool
     */
    protected $oldStorage = false;

    /**
     * @param string $storageId
     *
     * @return self
     */
    public static function create(string $storageId): self
    {
        $timestamp = time();

        return (new self())
            ->setStorageId($storageId)
            ->setTimestamp($timestamp)
            ->setDirectory(date('Y/m', $timestamp));
    }

    /**
     * @param string $storageId
     *
     * @return self
     */
    public function setStorageId(string $storageId): self
    {
        $this->storageId = $storageId;

        return $this;
    }

    /**
     * @return string
     */
    public function getStorageId(): string
    {
        return $this->storageId;
    }

    /**
     * @param int $timestamp
     *
     * @return self
     */
    public function setTimestamp(int $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @param string $directory
     *
     * @return self
     */
    public function setDirectory(string $directory): self
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @param bool $oldStorage
     *
     * @return self
     */
    public function setOldStorage(bool $oldStorage): self
    {
        $this->oldStorage = $oldStorage;

        return $this;
    }

    /**
     * @return bool
     */
    public function isOldStorage(): bool
    {
        return $this->oldStorage;
    }
}