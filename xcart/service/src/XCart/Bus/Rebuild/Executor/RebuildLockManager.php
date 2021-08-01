<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor;

use DirectoryIterator;
use Silex\Application;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class RebuildLockManager
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @param Application $app
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(Application $app)
    {
        return new self(
            $app['config']['root_dir']
        );
    }

    /**
     * @param string $rootDir
     */
    public function __construct($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @return bool
     */
    public function isAnyRebuildStartedFlagSet()
    {
        $dir = new DirectoryIterator($this->rootDir . 'var/');
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $basename = $fileinfo->getBasename();
                if (strpos($basename, '.rebuild.') === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function clearAnySetRebuildFlags()
    {
        $dir = new DirectoryIterator($this->rootDir . 'var/');
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $basename = $fileinfo->getBasename();
                if (strpos($basename, '.rebuild.') === 0) {
                    unlink($fileinfo->getRealPath());
                }
            }
        }

        return false;
    }

    /**
     * @param string $id
     */
    public function setRebuildStartedFlag($id)
    {
        $path = $this->getRebuildFlagPath($id);
        file_put_contents($path, null);
    }

    /**
     * @param string $id
     */
    public function unsetRebuildStartedFlag($id)
    {
        $path = $this->getRebuildFlagPath($id);
        unlink($path);
    }

    /**
     * @param string $id
     *
     * @return string
     */
    private function getRebuildFlagPath($id)
    {
        return sprintf('%svar/.rebuild.%s', $this->rootDir, $id);
    }
}
