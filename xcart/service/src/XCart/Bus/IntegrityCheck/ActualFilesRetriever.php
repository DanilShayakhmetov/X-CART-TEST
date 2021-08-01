<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\IntegrityCheck;

use Silex\Application;
use XCart\Bus\Domain\Module;
use XCart\Bus\Domain\Package;
use XCart\Bus\Domain\Package as DomainPackage;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ActualFilesRetriever
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var DomainPackage
     */
    private $package;

    /**
     * @var CoreIteratorBuilder
     */
    private $coreIteratorBuilder;

    /**
     * @var ServiceToolIteratorBuilder
     */
    private $serviceToolIteratorBuilder;

    /**
     * @param Application                $app
     * @param Package                    $package
     * @param CoreIteratorBuilder        $coreIteratorBuilder
     * @param ServiceToolIteratorBuilder $serviceToolIteratorBuilder
     *
     * @return ActualFilesRetriever
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        DomainPackage $package,
        CoreIteratorBuilder $coreIteratorBuilder,
        ServiceToolIteratorBuilder $serviceToolIteratorBuilder
    ): ActualFilesRetriever {
        return new self(
            $app['config']['root_dir'],
            $package,
            $coreIteratorBuilder,
            $serviceToolIteratorBuilder
        );
    }

    /**
     * @param                            $rootDir
     * @param DomainPackage              $package
     * @param CoreIteratorBuilder        $coreIteratorBuilder
     * @param ServiceToolIteratorBuilder $serviceToolIteratorBuilder
     */
    public function __construct(
        $rootDir,
        DomainPackage $package,
        CoreIteratorBuilder $coreIteratorBuilder,
        ServiceToolIteratorBuilder $serviceToolIteratorBuilder
    ) {
        $this->rootDir                    = $rootDir;
        $this->package                    = $package;
        $this->coreIteratorBuilder        = $coreIteratorBuilder;
        $this->serviceToolIteratorBuilder = $serviceToolIteratorBuilder;
    }

    /**
     * @param Module $module
     *
     * @return array
     */
    public function getActualFilesPaths($module): array
    {
        if (!empty($module->id) && $module->id === 'CDev-Core') {
            $iterator = $this->coreIteratorBuilder->getIterator();

            $result = [];

            foreach ($iterator as $absolutePath => $item) {
                /** @var \SplFileInfo $item */
                $prefix = $this->rootDir;

                if (0 === strpos($absolutePath, $prefix)) {
                    $absolutePath = substr($absolutePath, strlen($prefix));
                }
                $result[$absolutePath] = $item->getPathname();
            }

            return $result;
        }

        if (!empty($module->id) && $module->id === 'XC-Service') {
            $iterator = $this->serviceToolIteratorBuilder->getIterator();

            $result = [
                'service.php' => $this->rootDir . '/service.php',
            ];

            foreach ($iterator as $absolutePath => $item) {
                /** @var \SplFileInfo $item */
                $prefix = $this->rootDir;

                if (0 === strpos($absolutePath, $prefix)) {
                    $absolutePath = substr($absolutePath, strlen($prefix));
                }
                $result[$absolutePath] = $item->getPathname();
            }

            return $result;
        }

        $package = $this->package->fromModule($module);

        return $package->getModuleFilesList();
    }
}
