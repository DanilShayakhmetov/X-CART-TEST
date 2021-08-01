<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Domain\Storage;

interface StorageInterface
{
    /**
     * @param string $path
     * @param string $name
     *
     * @return StorageInterface
     */
    public function build($path, $name): StorageInterface;

    /**
     * @return array
     */
    public function read(): array;

    /**
     * @param array $data
     *
     * @return bool
     */
    public function write($data): bool;
}
