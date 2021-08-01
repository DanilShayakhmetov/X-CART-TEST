<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core\Archive;

abstract class AArchive
{
    /**
     * @return bool
     */
    abstract public function isApplicable(): bool;

    /**
     * @param string $path
     * @param string $root
     * @param array  $files
     * @param string $hash
     * @param array  $metadata
     *
     * @return bool
     */
    abstract public function pack($path, $root, $files, $hash, $metadata): bool;

    /**
     * @param string $path
     * @param string $destination
     *
     * @return bool
     */
    abstract public function unpack($path, $destination): bool;

    /**
     * @return bool
     */
    public function canCompress(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function getAvailableExtensions(): array
    {
        return ['.tgz', '.tar.gz', '.tar'];
    }

    /**
     * @return array
     */
    protected function getCompressedExtensions(): array
    {
        return ['.tgz', '.tar.gz'];
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getExtension($path): string
    {
        $result = '';

        foreach ($this->getAvailableExtensions() as $extension) {
            if (substr($path, -strlen($extension)) === $extension) {
                $result = $extension;
            }
        }

        return $result;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    protected function isCompressed($path): bool
    {
        return in_array($this->getExtension($path), $this->getCompressedExtensions(), true);
    }
}
