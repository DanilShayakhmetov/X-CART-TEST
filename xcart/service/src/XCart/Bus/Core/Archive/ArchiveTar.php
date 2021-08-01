<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core\Archive;

use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ArchiveTar extends AArchive
{
    /**
     * @var bool
     */
    private $canCompress;

    /**
     * @return bool
     */
    public function isApplicable(): bool
    {
        return class_exists(\Archive_Tar::class);
    }

    /**
     * @param string $path
     * @param string $destination
     *
     * @return bool
     */
    public function unpack($path, $destination): bool
    {
        $compression = $this->isCompressed($path) ? 'gz' : false;

        $archive = new \Archive_Tar($path, $compression);

        return $archive->extract($destination);
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
        $compression = $this->isCompressed($path) ? 'gz' : false;

        chdir($root);

        $archive = new \Archive_Tar($path, $compression);

        $rootLength = strlen(rtrim($root, '/') . '/');
        $files      = array_map(static function ($file) use ($rootLength) {
            return substr($file, $rootLength);
        }, $files);

        $archive->create($files);

        $archive->addString('.phar/.metadata.bin', serialize($metadata), time(), ['uid' => getmyuid(), 'gid' => getmygid(), 'mode' => 0000]);
        $archive->addString('.hash', $hash, time(), ['uid' => getmyuid(), 'gid' => getmygid(), 'mode' => 0644]);

        return true;
    }

    /**
    +     * @return bool
    +     */
    public function canCompress(): bool
    {
        if ($this->canCompress === null) {
            if (!extension_loaded('zlib')) {
                \PEAR::loadExtension('zlib');
            }

            $this->canCompress = extension_loaded('zlib');
        }

        return $this->canCompress;
     }
}
