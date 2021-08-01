<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use XCart\Bus\Client\MarketplaceClient;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\Bus\Domain\Module;
use XCart\Bus\Helper\HookFilter;
use XCart\Bus\Query\Context;
use XCart\Bus\Query\Data\ChangelogDataSource;
use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\Bus\Query\Data\KnownHashesCacheDataSource;
use XCart\Bus\Query\Data\MarketplaceModulesDataSource;
use XCart\Bus\Query\Data\ModulesDataSource;
use XCart\Bus\Query\Data\ScenarioDataSource;
use XCart\Bus\Query\Types\Output\AlertType;
use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;
use XCart\Bus\Rebuild\Upgrade\UpgradeEntryFactory;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class UpgradeResolver
{
    /**
     * @var ChangeUnitProcessor
     */
    private $changeUnitProcessor;

    /**
     * @var ScenarioDataSource
     */
    private $scenarioDataSource;

    /**
     * @var ModulesDataSource
     */
    private $modulesDataSource;

    /**
     * @var MarketplaceModulesDataSource
     */
    private $marketplaceModulesDataSource;

    /**
     * @var CoreConfigDataSource
     */
    private $coreConfigDataSource;

    /**
     * @var MarketplaceClient
     */
    private $marketplaceClient;

    /**
     * @var UpgradeEntryFactory
     */
    private $upgradeEntryFactory;

    /**
     * @var KnownHashesCacheDataSource
     */
    private $knownHashesCacheDataSource;

    /**
     * @var ChangelogDataSource
     */
    private $changelogDataSource;

    /**
     * @var HookFilter
     */
    private $hookFilter;

    /**
     * Runtime cache
     *
     * @var array
     */
    private $upgradeListByType = [];

    /**
     * @param ChangeUnitProcessor          $changeUnitProcessor
     * @param ScenarioDataSource           $scenarioDataSource
     * @param ModulesDataSource            $modulesDataSource
     * @param MarketplaceModulesDataSource $marketplaceModulesDataSource
     * @param CoreConfigDataSource         $coreConfigDataSource
     * @param MarketplaceClient            $marketplaceClient
     * @param UpgradeEntryFactory          $upgradeEntryFactory
     * @param KnownHashesCacheDataSource   $knownHashesCacheDataSource
     * @param ChangelogDataSource          $changelogDataSource
     * @param HookFilter                   $hookFilter
     */
    public function __construct(
        ChangeUnitProcessor $changeUnitProcessor,
        ScenarioDataSource $scenarioDataSource,
        ModulesDataSource $modulesDataSource,
        MarketplaceModulesDataSource $marketplaceModulesDataSource,
        CoreConfigDataSource $coreConfigDataSource,
        MarketplaceClient $marketplaceClient,
        UpgradeEntryFactory $upgradeEntryFactory,
        KnownHashesCacheDataSource $knownHashesCacheDataSource,
        ChangelogDataSource $changelogDataSource,
        HookFilter $hookFilter
    ) {
        $this->changeUnitProcessor          = $changeUnitProcessor;
        $this->scenarioDataSource           = $scenarioDataSource;
        $this->modulesDataSource            = $modulesDataSource;
        $this->marketplaceModulesDataSource = $marketplaceModulesDataSource;
        $this->coreConfigDataSource         = $coreConfigDataSource;
        $this->marketplaceClient            = $marketplaceClient;
        $this->upgradeEntryFactory          = $upgradeEntryFactory;
        $this->knownHashesCacheDataSource   = $knownHashesCacheDataSource;
        $this->changelogDataSource          = $changelogDataSource;
        $this->hookFilter                   = $hookFilter;
    }

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return array
     * @throws Exception
     *
     * @Resolver()
     */
    public function resolveList($value, $args, $context, ResolveInfo $info): array
    {
        $result = [];

        $modules = $this->getUpgradeListByType($args['type'], $args['moduleId'] ?? '');

        foreach ($modules as $module) {
            $result[] = $this->upgradeEntryFactory->buildEntry(
                $module->id,
                $module
            );
        }

        return $result;
    }

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return int
     * @throws Exception
     *
     * @Resolver()
     */
    public function getUpgradeEntriesCount($value, $args, $context, ResolveInfo $info): int
    {
        $modules = array_filter($this->getUpgradeListByType($args['type']), static function ($item) {
            return $item['version'] !== null;
        });

        $self = array_filter($this->getUpgradeListByType('self'), static function ($item) {
            return $item['version'] !== null;
        });

        return count($modules) + count($self);
    }

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return array
     * @throws Exception
     *
     * @Resolver()
     */
    public function fillScenario($value, $args, $context, ResolveInfo $info): array
    {
        $list = $this->getUpgradeListByType(
            $args['type']
        );

        $ids = $args['ids'] ?? null;

        if (is_array($ids)) {
            $list = array_filter($list, static function ($entry) use ($ids) {
                return in_array($entry['id'], $ids, true);
            });
        }

        $changeUnits = array_map(function ($entry) {
            return $this->getChangeUnit($entry);
        }, $list);

        $scenario         = $this->changeUnitProcessor->process([], $changeUnits);
        $scenario['id']   = $args['scenarioId'];
        $scenario['type'] = 'upgrade';

        $this->scenarioDataSource->saveOne($scenario);

        return $scenario;
    }

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return array
     * @throws Exception
     *
     * @Resolver()
     */
    public function getAvailableUpgradeTypes($value, $args, $context, ResolveInfo $info): array
    {
        $result = [];

        $types = $this->getTypes();
        foreach ($types as $type => $typeIndex) {
            $modules = $this->getUpgradeListByType($type);

            if ($modules && $this->hasEntriesOfType($modules, $typeIndex)) {
                $result[$type] = [
                    'name'   => $type,
                    'weight' => $typeIndex,
                    'count'  => count($modules),
                ];
            }
        }

        uasort($result, static function ($a, $b) {
            return $a['weight'] < $b['weight'];
        });

        $serviceModule = $this->modulesDataSource->findOne('XC-Service');
        if ($serviceModule && $serviceModule['version']) {
            $result['self'] = [
                'name'   => 'self',
                'weight' => '0',
                'count'  => 1,
            ];
        }

        return $result;
    }

    /**
     * @param mixed       $value
     * @param array       $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return array
     * @throws Exception
     *
     * @Resolver()
     */
    public function requestForUpgrade($value, $args, Context $context, ResolveInfo $info): array
    {
        if (!($context->mode === Context::ACCESS_MODE_FULL)) {
            return [];
        }

        /** @var Module|null $module */
        $module = isset($args['id']) ? $this->modulesDataSource->findOne($args['id']) : null;

        if ($module) {
            $result = $this->marketplaceClient->requestForUpgrade(
                $context->adminEmail,
                [$module->id => $module]
            );

            if ($result) {
                $requestForUpgrade              = $this->coreConfigDataSource->requestForUpgrade;
                $requestForUpgrade[$module->id] = time() + 86400;

                $this->coreConfigDataSource->requestForUpgrade = $requestForUpgrade;

                return [
                    [
                        'type'    => 'success',
                        'message' => 'request-for-upgrade.success',
                        'params'  => AlertType::prepareParams([]),
                    ],
                ];
            }
        }

        return [
            [
                'type'    => 'warning',
                'message' => 'request-for-upgrade.warning',
                'params'  => AlertType::prepareParams([]),
            ],
        ];
    }

    /**
     * @param string $type
     * @param string $id
     *
     * @return Module[]
     */
    private function getUpgradeListByType($type, $id = ''): array
    {
        if (empty($id) && isset($this->upgradeListByType[$type])) {
            return $this->upgradeListByType[$type];
        }

        $filters = [
            'installed' => true,
            'actions'   => true,
        ];

        /** @var Module[] $modules */
        $modules = array_filter(
            $this->modulesDataSource->getSlice($type, $filters, ['version desc', 'revisionDate desc']),
            static function ($item) use ($type) {
                /** @var Module $item */
                return ($type === 'self'
                        ? $item->id === 'XC-Service'
                        : $item->id !== 'XC-Service'
                    )
                    && $item->version !== null;
            }
        );

        if ($type === 'self') {
            $modules = array_filter(
                $modules,
                static function ($item) {
                    /** @var Module $item */
                    return $item->id === 'XC-Service';
                }
            );
        } elseif ($type === 'postponed' && $id) {
            $modules = array_filter(
                $modules,
                static function ($item) use ($id) {
                    /** @var Module $item */
                    return $item->id === $id;
                }
            );
        }

        $disabledModules = array_filter(
            $modules,
            static function ($item) {
                /** @var Module $item */
                return $item->enabled === false;
            }
        );

        if ($disabledModules) {
            $hashes = $this->getHashes($disabledModules);
            foreach ($hashes as $moduleId => $hash) {
                if (isset($modules[$moduleId])) {
                    $modules[$moduleId]->hash = $hash;
                }
            }
        }

        $changelog = $this->getChangelog($modules);
        foreach ($changelog as $moduleId => $changelogData) {
            if (isset($modules[$moduleId])) {
                $modules[$moduleId]->changelog = $changelogData;
            }
        }

        if (empty($id)) {
            $this->upgradeListByType[$type] = $modules;
        }

        return $modules;
    }

    /**
     * @param Module[] $modules
     *
     * @return array
     */
    private function getHashes(array $modules): array
    {
        $result    = [];
        $toRequest = [];

        foreach ($modules as ['id' => $id, 'version' => $version]) {
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

    /**
     * @param Module[] $modules
     *
     * @return array
     */
    private function getChangelog(array $modules): array
    {
        $result    = [];
        $toRequest = [];

        foreach ($modules as $module) {
            $id               = $module->id;
            $version          = $module->version;
            $installedVersion = $module->installedVersion;

            $hash = $this->changelogDataSource->find(md5($id . '|' . $installedVersion . '|' . $version));

            if ($hash) {
                $result[$id] = $hash;

            } elseif ($this->marketplaceModulesDataSource->findByVersion($id, $version)) {
                $toRequest[$id] = [
                    'author'      => $module->author,
                    'name'        => $module->name,
                    'versionFrom' => $installedVersion,
                    'versionTo'   => $version,
                    'infoType'    => ['changelog'],
                ];
            }
        }

        if ($toRequest) {
            $changelog = $this->marketplaceClient->getVersionInfo($toRequest);
            foreach ($changelog as $changelogData) {
                $id = Module::buildModuleId($changelogData['author'], $changelogData['name']);

                $result[$id] = array_map(
                    'trim',
                    $changelogData['changelog']
                );

                $this->changelogDataSource->saveOne(
                    $result[$id],
                    md5($id . '|' . $toRequest[$id]['versionFrom'] . '|' . $toRequest[$id]['versionTo'])
                );
            }
        }

        return $result;
    }

    /**
     * @param array[] $list
     * @param int     $typeIndex
     *
     * @return bool
     */
    private function hasEntriesOfType($list, $typeIndex): bool
    {
        foreach ($list as $entry) {
            if ($this->isEntryOfType($entry, $typeIndex)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $entry
     * @param int   $typeIndex
     *
     * @return bool
     */
    private function isEntryOfType($entry, $typeIndex): bool
    {
        $version          = explode('.', $entry['version']);
        $installedVersion = explode('.', $entry['installedVersion']);

        for ($index = 0; $index <= $typeIndex; $index++) {
            if ($index < $typeIndex && $version[$typeIndex] === $installedVersion[$typeIndex]) {
                continue;
            }

            if ($index === $typeIndex && $version[$typeIndex] !== $installedVersion[$typeIndex]) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $entry
     *
     * @return array
     */
    private function getChangeUnit($entry): array
    {
        if ($entry['enabled']
            || !$this->hasHooks($entry)
            || Module::isPreviuosMajorVersion($entry['installedVersion'], $this->coreConfigDataSource->version)
        ) {
            return [
                'id'      => $entry['id'],
                'upgrade' => true,
                'version' => $entry['version'],
            ];
        }

        return [
            'id'     => $entry['id'],
            'remove' => true,
        ];
    }

    /**
     * @param $entry
     *
     * @return bool
     */
    private function hasHooks($entry): bool
    {
        return $this->hookFilter->hasHooks(
            array_keys($entry['hash']),
            $entry['id'],
            $entry['installedVersion'],
            $entry['version']
        );
    }

    /**
     * @return array
     */
    private function getTypes(): array
    {
        return [
            'core'  => 0,
            'major' => 1,
            'minor' => 2,
            'build' => 3,
        ];
    }
}
