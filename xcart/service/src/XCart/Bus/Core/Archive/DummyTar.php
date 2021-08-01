<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core\Archive;

class DummyTar extends AArchive
{
    /**
     * @return bool
     */
    public function isApplicable(): bool
    {
        return true;
    }

    /**
     * @param string $path
     * @param string $destination
     *
     * @return bool
     */
    public function unpack($path, $destination): bool
    {
        return false;
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
        return false;
    }
}
