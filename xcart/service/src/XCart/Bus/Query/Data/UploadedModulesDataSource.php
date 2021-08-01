<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data;

use Silex\Application;
use XCart\Bus\Domain\Module;
use XCart\Bus\Domain\ModuleInfoProvider;
use XCart\Bus\Domain\Package;
use XCart\Bus\Domain\Storage\StorageInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class UploadedModulesDataSource extends SerializedDataSource
{
    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    /**
     * @param Application        $app
     * @param ModuleInfoProvider $moduleInfoProvider
     * @param StorageInterface   $storage
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        ModuleInfoProvider $moduleInfoProvider,
        StorageInterface $storage
    ) {
        return new static(
            $moduleInfoProvider,
            $storage->build($app['config']['cache_dir'], 'busUploadedModulesStorage')
        );
    }

    /**
     * @param ModuleInfoProvider $moduleInfoProvider
     * @param StorageInterface   $storage
     */
    public function __construct(
        ModuleInfoProvider $moduleInfoProvider,
        StorageInterface $storage
    ) {
        parent::__construct($storage);

        $this->moduleInfoProvider = $moduleInfoProvider;
    }

    /**
     * @param mixed       $value
     * @param string|null $id
     *
     * @return bool
     */
    public function saveOne($value, $id = null): bool
    {
        $data      = $this->getAll();
        $data[$id ?? $this->buildItemId($value)] = [$value];

        return $this->saveAll($data);

    }

    /**
     * @param Package $package
     *
     * @throws \Exception
     */
    public function saveFromPackage(Package $package): void
    {
        $module = $package->getModule();

        $this->saveOne($module, $module->id);
    }

    /**
     * @return bool
     */
    public function fillWithAllPossibleModules(): bool
    {
        $allPossibleModules = $this->moduleInfoProvider->getAllPossibleModules();

        $data = $this->getAll();
        foreach ($allPossibleModules as $moduleId) {
            $info = $this->moduleInfoProvider->getModuleInfo($moduleId);

            if ($info) {
                $data[$moduleId] = [new Module($info)];
            }
        }

        return $this->saveAll($data);
    }
}
