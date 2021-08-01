<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core\Archive;

use XCart\Bus\System\Filesystem;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class PharTar extends AArchive
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var bool
     */
    private $canCompress;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return bool
     */
    public function isApplicable(): bool
    {
        return class_exists(\PharData::class);
    }

    /**
     * @param string $path
     * @param string $destination
     *
     * @return bool
     */
    public function unpack($path, $destination): bool
    {
        $phar = new \PharData($path);

        $phar->extractTo($destination);

        return true;
    }

    /**
     * @param string $path
     * @param string $root
     * @param array  $files
     * @param string $hash
     * @param array  $metadata
     *
     * @return bool
     */
    public function pack($path, $root, $files, $hash, $metadata): bool
    {
        $phar = new \PharData($path);

        $rootLength = strlen(rtrim($root, '/') . '/');
        foreach ($files as $file) {
            $phar->addFile($file, substr($file, $rootLength));
        }

        $phar->setMetadata($metadata);

        $phar->addFromString('.hash', $hash);

        if ($this->isCompressed($path)) {
            $phar = $phar->compress(\Phar::GZ);
            // Truncates version, see https://bugs.php.net/bug.php?id=58852
            $this->filesystem->rename($phar->getPath(), $path, true);
        }

        return true;
    }

    /**
     * @return bool
     */
    public function canCompress(): bool
    {
        if ($this->canCompress === null) {
            $this->canCompress = \Phar::canCompress(\Phar::GZ);
        }

        return $this->canCompress;
    }
}
