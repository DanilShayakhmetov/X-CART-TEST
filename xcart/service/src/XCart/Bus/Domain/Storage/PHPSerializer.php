<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Domain\Storage;

use Symfony\Component\Filesystem\Exception\IOException;
use XCart\Bus\System\FilesystemInterface;
use XCart\SilexAnnotations\Annotations\Service;
use Psr\Log\LoggerInterface;

/**
 * @Service\Service()
 */
class PHPSerializer implements StorageInterface
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param FilesystemInterface $filesystem
     * @param LoggerInterface     $logger
     */
    public function __construct(
        FilesystemInterface $filesystem,
        LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
        $this->logger     = $logger;
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
            return (array) unserialize($fileContent, ['allowed_classes' => true]);
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
        try {
            if ($data) {
                $this->filesystem->dumpFile($this->path, serialize((array) $data));

            } else {
                $this->filesystem->remove($this->path);
            }
        } catch (IOException $e) {
            $this->logger->critical(
                'Writing data error',
                [
                    'message' => $e->getMessage(),
                    'path'    => $this->path,
                ]
            );

            return false;
        }

        return true;
    }

    /**
     * @param string $path
     * @param string $name
     */
    private function init($path, $name): void
    {
        $this->filesystem->mkdir($path);

        $this->path = $path . '/' . $name . '.data';
    }
}
