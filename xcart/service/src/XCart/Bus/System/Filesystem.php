<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\System;

use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class Filesystem extends \Symfony\Component\Filesystem\Filesystem implements FilesystemInterface
{
    /**
     * @param string $path
     *
     * @return string
     */
    public function getNearestExistingDirectory($path, $stopPath = ''): string
    {
        do {
            $path = dirname($path);
        } while (!is_dir($path) && $path !== $stopPath);

        return $path;
    }
}
