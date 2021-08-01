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
use XCart\Bus\Domain\Storage\StorageInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class InstalledModulesDataSource extends SerializedDataSource
{
    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    /**
     * @var CoreConfigDataSource
     */
    private $coreConfigDataSource;

    /**
     * Trial period (30 days)
     */
    const TRIAL_PERIOD = 2592000;

    /**
     * @param Application          $app
     * @param ModuleInfoProvider   $moduleInfoProvider
     * @param CoreConfigDataSource $coreConfigDataSource
     * @param StorageInterface     $storage
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        ModuleInfoProvider $moduleInfoProvider,
        CoreConfigDataSource $coreConfigDataSource,
        StorageInterface $storage
    ) {
        return new static(
            $moduleInfoProvider,
            $coreConfigDataSource,
            $storage->build($app['config']['cache_dir'], 'busInstalledModulesStorage')
        );
    }

    /**
     * @param ModuleInfoProvider   $moduleInfoProvider
     * @param CoreConfigDataSource $coreConfigDataSource
     * @param StorageInterface     $storage
     */
    public function __construct(
        ModuleInfoProvider $moduleInfoProvider,
        CoreConfigDataSource $coreConfigDataSource,
        StorageInterface $storage
    ) {
        parent::__construct($storage);

        $this->moduleInfoProvider   = $moduleInfoProvider;
        $this->coreConfigDataSource = $coreConfigDataSource;
    }

    /**
     * Checks if module record is fine and contains all the needed fields
     *
     * @param $module
     *
     * @return bool
     */
    public static function isValidRecord(Module $module): bool
    {
        return isset($module->id, $module->author, $module->name, $module->installed, $module->version)
            && $module->installed === true;
    }

    /**
     * @return Module[]
     */
    public function getAll(): array
    {
        /** @var array|Module[] $data */
        $data = parent::getAll();

        if ($this->coreConfigDataSource->freshInstall) {
            $installationDate = time();
            foreach ($data as $module) {
                $module->installedDate = $installationDate;
                $module->enabledDate   = $installationDate;
            }

            $this->saveAll($data);
            $this->coreConfigDataSource->freshInstall = false;
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getLanguages(): array
    {
        $languages = ['en'];

        foreach ($this->getAll() as $module) {
            if (!$module->enabled || !isset($module->service['Language'])) {
                continue;
            }

            $languages[] = $module['service']['Language']['code'];
        }

        return $languages;
    }

    /**
     * @param Module $module
     *
     * @throws \Exception
     */
    public function installModule(Module $module): void
    {
        if (static::isValidRecord($module)) {
            $this->saveOne($module, $module->id);
        } else {
            throw new \Exception('Invalid module record given for installation: ' . PHP_EOL . var_export($module, true));
        }
    }

    /**
     * @return array
     */
    public function getInstalledVersions(): array
    {
        return array_values(array_map(function ($module) {
            /** @var Module $module */
            [$core, $major, $minor, $build] = Module::explodeVersion($module->version);

            return [
                'author'  => $module->author,
                'name'    => $module->name,
                'major'   => $core . '.' . $major,
                'minor'   => $minor . '.' . $build,
                'enabled' => $module->enabled,
            ];
        }, $this->getAll()));
    }

    /**
     * @param string $coreVersion
     * @param string $serviceVersion
     *
     * @return bool
     */
    public function fillWithCoreModules(string $coreVersion, string $serviceVersion): bool
    {
        $data = $this->getAll();

        $core = Module::generateCoreModule($coreVersion);

        $data[$core->id] = $core;

        $service = Module::generateServiceModule($serviceVersion);

        $data[$service->id] = $service;

        return $this->saveAll($data);
    }

    /**
     * @return array
     */
    public function updateModulesData(): array
    {
        $result = [];

        /** @var Module[] $data */
        $data               = $this->getAll();
        $allPossibleModules = $this->moduleInfoProvider->getAllPossibleModules();

        foreach ($allPossibleModules as $moduleId) {
            $info = $this->moduleInfoProvider->getModuleInfo($moduleId);
            if ($info) {
                if (!isset($data[$moduleId])) {
                    $result[] = $moduleId;

                    $data[$moduleId]            = new Module($info);
                    $data[$moduleId]->installed = true;
                    $data[$moduleId]->enabled   = false;
                } else {
                    $data[$moduleId]->merge($info);
                }
            }
        }

        $this->saveAll($data);

        return $result;
    }

    /**
     * @return array
     */
    public function removeMissedModules(): array
    {
        $result = [];

        $data               = $this->getAll();
        $allPossibleModules = $this->moduleInfoProvider->getAllPossibleModules();

        foreach ($data as $moduleId => $module) {
            if ($moduleId === 'CDev-Core' || $moduleId === 'XC-Service') {
                continue;
            }

            if (!in_array($moduleId, $allPossibleModules, true)
                || !$this->moduleInfoProvider->getModuleInfo($moduleId)
            ) {
                $result[] = $moduleId;

                unset($data[$moduleId]);
            }
        }

        $this->saveAll($data);

        return $result;
    }
}
