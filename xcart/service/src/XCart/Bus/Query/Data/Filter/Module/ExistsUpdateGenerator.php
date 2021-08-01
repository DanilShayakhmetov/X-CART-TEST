<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use Iterator;
use XCart\Bus\Client\MarketplaceClient;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\KnownHashesCacheDataSource;
use XCart\Bus\Query\Data\MarketplaceModulesDataSource;
use XCart\Bus\Query\Data\ModulesDataSource;
use XCart\Bus\Rebuild\Upgrade\UpgradeEntryFactory;
use XCart\SilexAnnotations\Annotations\Service;
use XCart\Bus\Core\Annotations\DataSourceFilter;
use XCart\Bus\Query\Data\Filter\AFilterGenerator;

/**
 * @DataSourceFilter(name="existsUpdate")
 * @Service\Service()
 */
class ExistsUpdateGenerator extends AFilterGenerator
{
    /**
     * @var ModulesDataSource
     */
    protected $modulesDataSource;

    /**
     * @var UpgradeEntryFactory
     */
    protected $upgradeEntryFactory;

    /**
     * @var KnownHashesCacheDataSource
     */
    protected $knownHashesCacheDataSource;

    /**
     * @var MarketplaceModulesDataSource
     */
    protected $marketplaceModulesDataSource;

    /**
     * @var MarketplaceClient
     */
    protected $marketplaceClient;

    /**
     * @var array
     */
    private $typeModules = [];

    /**
     * FlattenGenerator constructor.
     *
     * @param ModulesDataSource            $modulesDataSource
     * @param UpgradeEntryFactory          $upgradeEntryFactory
     * @param KnownHashesCacheDataSource   $knownHashesCacheDataSource
     * @param MarketplaceModulesDataSource $marketplaceModulesDataSource
     * @param MarketplaceClient            $marketplaceClient
     */
    public function __construct(
        ModulesDataSource $modulesDataSource,
        UpgradeEntryFactory $upgradeEntryFactory,
        KnownHashesCacheDataSource $knownHashesCacheDataSource,
        MarketplaceModulesDataSource $marketplaceModulesDataSource,
        MarketplaceClient $marketplaceClient
    ) {
        $this->modulesDataSource            = $modulesDataSource;
        $this->upgradeEntryFactory          = $upgradeEntryFactory;
        $this->knownHashesCacheDataSource   = $knownHashesCacheDataSource;
        $this->marketplaceModulesDataSource = $marketplaceModulesDataSource;
        $this->marketplaceClient            = $marketplaceClient;
    }

    /**
     * @param $type
     *
     * @return Module[]
     */
    protected function getFlattenModules($type)
    {
        if (isset($this->typeModules[$type])) {
            return $this->typeModules[$type];
        }

        $filters = ['installed' => true];

        /** @var Module[] $modules */
        $modules = array_filter(
            $this->modulesDataSource->getSlice($type, $filters),
            static function ($item) use ($type) {
                /** @var Module $item */
                return ($type === 'self'
                        ? $item->id === 'XC-Service'
                        : $item->id !== 'XC-Service'
                    )
                    && $item->version !== null;
            }
        );

        $hashes = $this->getHashes($modules);
        foreach ($hashes as $moduleId => $hash) {
            if (isset($modules[$moduleId])) {
                $modules[$moduleId]->hash = $hash;
            }
        }

        $this->typeModules[$type] = array_filter($modules, function ($module) {
            return !$this
                ->upgradeEntryFactory
                ->buildEntry($module->id, $module)
                ->canUpgrade;
        });

        return $this->typeModules[$type];
    }

    /**
     * @param Iterator $iterator
     * @param string   $field
     * @param mixed    $data
     *
     * @return ExistsUpdate
     */
    public function __invoke(Iterator $iterator, $field, $data)
    {
        $modules = $this->getFlattenModules($data);

        return new ExistsUpdate($iterator, $field, $data, $modules);
    }

    /**
     * @param array $modules
     *
     * @return array
     */
    private function getHashes(array $modules): array
    {
        $result    = [];
        $toRequest = [];

        foreach ($modules as ['id' => $id, 'version' => $version, 'enabled' => $enabled]) {
            if ($enabled === true) {
                continue;
            }
            $hash = $this->knownHashesCacheDataSource->find(md5($id . '|' . $version));

            if ($hash) {
                $result[$id] = $hash;

            } elseif ($this->marketplaceModulesDataSource->findByVersion($id, $version)) {
                $toRequest[$id] = ['id' => $id, 'version' => $version];
            }
        }

        if ($toRequest) {
            $hashes = $this->marketplaceClient->getHashesBatch($toRequest);
            foreach ($hashes as $id => $hash) {
                $result[$id] = $hash;
                $this->knownHashesCacheDataSource->saveOne($hash, md5($id . '|' . $toRequest[$id]['version']));
            }
        }

        return $result;
    }
}