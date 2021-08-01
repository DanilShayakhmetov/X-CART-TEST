<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Domain\Storage;

use XCart\Bus\System\FilesystemInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class JsonSerializer implements StorageInterface
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var string
     */
    private $path;

    /**
     * @param FilesystemInterface $filesystem
     */
    public function __construct(
        FilesystemInterface $filesystem
    ) {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $path
     * @param string $name
     *
     * @return StorageInterface
     */
    public function build($path, $name): StorageInterface
    {
        $storage = clone $this;

        $storage->init($path, $name);

        return $storage;
    }

    /**
     * @return array
     */
    public function read(): array
    {
        $fileContent = $this->filesystem->exists($this->path) ? file_get_contents($this->path) : null;

        if (!empty($fileContent)) {
            return json_decode($fileContent, true);
        }

        return [];
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    public function write($data): bool
    {
        $this->filesystem->dumpFile($this->path, json_encode($data, JSON_PRETTY_PRINT));

        return true;
    }

    /**
     * @param string $path
     * @param string $name
     */
    private function init($path, $name): void
    {
        $this->filesystem->mkdir($path);

        $this->path = $path . '/' . $name . '.json';
    }
}
