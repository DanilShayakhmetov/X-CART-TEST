<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\IntegrityCheck;

use Silex\Application;
use XCart\Bus\Domain\ModuleInfoProvider;
use XCart\Bus\System\Filesystem;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ServiceToolIteratorBuilder
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @param Application $app
     *
     * @return ServiceToolIteratorBuilder
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app
    ) {
        return new self(
            $app['config']['root_dir']
        );
    }

    /**
     * @param string     $rootDir
     */
    public function __construct(
        $rootDir
    ) {
        $this->rootDir = $rootDir;
    }

    /**
     * Return iterator to walk through directories
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        $iterator = new \RecursiveDirectoryIterator($this->rootDir . 'service', \FilesystemIterator::SKIP_DOTS);

        $iterator = new \RecursiveCallbackFilterIterator($iterator, static function ($file, $key, $iterator) {
            if ($iterator->hasChildren() && $file->getFilename() === 'spa') {
                return false;
            }

            return true;
        });

        return new \RecursiveIteratorIterator(
            $iterator,
            \RecursiveIteratorIterator::LEAVES_ONLY
        );
    }
}
