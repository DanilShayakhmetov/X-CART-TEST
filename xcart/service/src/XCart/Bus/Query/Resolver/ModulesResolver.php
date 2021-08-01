<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use ArrayIterator;
use GraphQL\Type\Definition\ResolveInfo;
use Iterator;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\Bus\Domain\Module;
use XCart\Bus\IntegrityCheck\IntegrityViolationProcessor;
use XCart\Bus\Query\Context;
use XCart\Bus\Query\Data\Flatten\Flatten;
use XCart\Bus\Query\Data\IntegrityCheckDataDataSource;
use XCart\Bus\Query\Data\IntegrityCheckModulesDataSource;
use XCart\Bus\Query\Data\LicenseDataSource;
use XCart\Bus\Query\Data\ModulesDataSource;
use XCart\Bus\Query\Data\ScenarioDataSource;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ModulesResolver
{
    /**
     * @var IntegrityViolationProcessor
     */
    private $integrityViolationProcessor;

    /**
     * @var ModulesDataSource
     */
    private $modulesDataSource;

    /**
     * @var ScenarioDataSource
     */
    private $scenarioDataSource;

    /**
     * @var LicenseDataSource
     */
    private $licenseDataSource;

    /**
     * @var IntegrityCheckModulesDataSource
     */
    private $integrityCheckModulesDataSource;

    /**
     * @var IntegrityCheckDataDataSource
     */
    private $integrityCheckDataDataSource;

    /**
     * @var array
     */
    private $actualModules;

    /**
     * @param ModulesDataSource               $modulesDataSource
     * @param ScenarioDataSource              $scenarioDataSource
     * @param LicenseDataSource               $licenseDataSource
     * @param IntegrityViolationProcessor     $integrityViolationProcessor
     * @param IntegrityCheckModulesDataSource $integrityCheckModulesDataSource
     * @param IntegrityCheckDataDataSource    $integrityCheckDataDataSource
     */
    public function __construct(
        ModulesDataSource $modulesDataSource,
        ScenarioDataSource $scenarioDataSource,
        LicenseDataSource $licenseDataSource,
        IntegrityViolationProcessor $integrityViolationProcessor,
        IntegrityCheckModulesDataSource $integrityCheckModulesDataSource,
        IntegrityCheckDataDataSource $integrityCheckDataDataSource
    ) {
        $this->modulesDataSource               = $modulesDataSource;
        $this->scenarioDataSource              = $scenarioDataSource;
        $this->licenseDataSource               = $licenseDataSource;
        $this->integrityViolationProcessor     = $integrityViolationProcessor;
        $this->integrityCheckModulesDataSource = $integrityCheckModulesDataSource;
        $this->integrityCheckDataDataSource    = $integrityCheckDataDataSource;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param             $value
     * @param             $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return array
     *
     * @Resolver()
     */
    public function resolvePage($value, $args, Context $context, ResolveInfo $info): array
    {
        $integrityCheck = $args['integrityCheck'] ?? false;
        unset($args['integrityCheck']);

        $version = $args['version'] ?? Flatten::RULE_LAST;
        unset($args['version']);

        $sorters = $args['sort'] ?? [];
        unset($args['sort']);

        $limit = $args['limit'] ?? [];
        unset($args['limit']);

        $scenario = $args['scenario'] ?? '';
        unset($args['scenario']);

        $iterator = $this->modulesDataSource->getFlatten($version);

        $iterator = $this->mergeWithScenario($iterator, $scenario);

        $iterator = $this->updateDependencies($iterator);

        $iterator = $this->updateLicenses($iterator);

        $args['excludeById'] = array_merge(
            $args['excludeById'] ?? [],
            ['CDev-Core', 'XC-Service']
        );

        if (!($context->mode & Context::ACCESS_MODE_WRITE)) {
            $args['actions']         = true;
            $args['readOnlyActions'] = true;

        } else {
            $args['actions'] = true;
        }

        $args['purchaseUrl'] = true;

        $args = ['language' => $context->languageCode ?: 'en'] + $args;

        if (isset($args['enabled']) && $args['enabled'] === 'recent') {
            $args['enabled'] = 'enabled';
            $sorters         = ['enabledDate desc', 'moduleName asc'];
        }

        $iterator = $this->modulesDataSource->filteredIterator($iterator, $args);

        $result = $this->modulesDataSource->sliceIterator(
            $this->modulesDataSource->sortIterator($iterator, $sorters),
            $limit
        );

        $modules = iterator_to_array($iterator);

        if ($integrityCheck) {
            $this->integrityCheckModulesDataSource->saveAll([
                'count'   => \count($modules),
                'modules' => $modules,
            ]);
            $this->integrityCheckDataDataSource->saveAll([]);
        }

        return [
            'count'   => \count($modules),
            'modules' => $result,
        ];
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param             $value
     * @param             $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return Module|null
     *
     * @Resolver()
     */
    public function resolveModule($value, $args, Context $context, ResolveInfo $info): ?Module
    {
        if (isset($args['id'])) {
            return $this->getModule($args['id'], $context, $args['language'] ?? null);
        }

        return null;
    }

    /**
     * @param string  $id
     * @param Context $context
     * @param string  $language
     *
     * @return Module|null
     */
    public function getModule($id, Context $context, $language = null): ?Module
    {
        if ($this->actualModules === null) {
            $filters = [
                'language'    => $language ?: $context->languageCode ?: 'en',
                'purchaseUrl' => true,
                'actions'     => true,
            ];

            if (!($context->mode & Context::ACCESS_MODE_WRITE)) {
                $filters['readOnlyActions'] = true;
            }

            $actualModulesIterator = $this->modulesDataSource->filteredIterator(
                $this->updateDependencies($this->modulesDataSource->getFlatten()),
                $filters
            );

            $this->actualModules = iterator_to_array($actualModulesIterator);
        }

        foreach ($this->actualModules as $module) {
            if ($module->id === $id) {
                return $module;
            }
        }

        return null;
    }

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param             $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return array
     *
     * @Resolver()
     */
    public function getIntegrityCheckCache($value, $args, $context, ResolveInfo $info): array
    {
        if (!($context->mode === Context::ACCESS_MODE_FULL)) {
            return [];
        }

        $modulesPage = $this->integrityCheckModulesDataSource->getAll();
        $data        = $this->integrityCheckDataDataSource->getAll();

        return [
            'modulesPage' => $modulesPage,
            'data'        => $data,
        ];
    }

    /**
     * @param Module      $value
     * @param             $args
     * @param             $context
     * @param ResolveInfo $info
     *
     * @return array
     *
     * @Resolver()
     */
    public function getIntegrityViolations($value, $args, $context, ResolveInfo $info): array
    {
        $start = null;
        $end   = null;

        if (!empty($args['limit']) && \is_array($args['limit']) && \count($args['limit']) === 2) {
            [$start, $end] = $args['limit'];
        }

        $violationsStructure = $this->integrityViolationProcessor->getViolationsStructure($value, $start, $end);
        $this->integrityCheckDataDataSource->appendEntries($violationsStructure, $value);

        return $violationsStructure;
    }

    /**
     * @param Iterator $iterator
     *
     * @return Iterator
     */
    private function updateDependencies($iterator): Iterator
    {
        $data = iterator_to_array($iterator);

        /**
         * @var string $key
         * @var Module $module
         */
        foreach ($data as $key => $module) {
            foreach ((array) $module->incompatibleWith as $moduleId) {
                /** @var Module $targetModule */
                $targetModule = $data[$moduleId] ?? null;
                if ($targetModule && !\in_array($key, $targetModule->incompatibleWith, true)) {
                    $incompatibles   = $targetModule->incompatibleWith;
                    $incompatibles[] = $key;

                    $targetModule->incompatibleWith = $incompatibles;
                }
            }
        }

        /**
         * @var string $key
         * @var Module $module
         */
        foreach ($data as $key => $module) {
            foreach ((array) $module->dependsOn as $moduleId) {
                if (!isset($data[$moduleId])) {
                    continue;
                }

                $targetModule = $data[$moduleId];
                if ($targetModule->scenarioState['enabled']) {
                    $module->dependsOn = array_diff($module->dependsOn, [$moduleId]);
                }

                if ($module->scenarioState['enabled']) {
                    $requirements   = $targetModule->requiredBy;
                    $requirements[] = $key;

                    $targetModule->requiredBy = $requirements;
                }
            }

            if ($module->scenarioState['enabled']) {
                $module->incompatibleWith = [];
            } else {
                $module->incompatibleWith = array_filter(
                    $module->incompatibleWith,
                    function ($item) use ($data) {
                        /** @var Module $incompatibles */
                        $incompatibles = $data[$item] ?? null;

                        return $incompatibles ? $incompatibles->scenarioState['enabled'] : false;
                    }
                );
            }
        }

        return new ArrayIterator($data);
    }

    /**
     * @param Iterator $iterator
     *
     * @return Iterator
     */
    private function updateLicenses($iterator): Iterator
    {
        $data = iterator_to_array($iterator);

        /**
         * @var string $key
         * @var Module $module
         */
        foreach ($data as $key => $module) {
            $license = $this->licenseDataSource->findBy([
                'author' => $module->author,
                'name'   => $module->name,
                'active' => true,
            ]);

            $module->license    = $license['keyValue'] ?? '';
            $module->expiration = $license['keyData']['expDate'] ?? '';
            $module->edition    = $license['keyData']['editionName'] ?? '';
        }

        return new ArrayIterator($data);
    }

    /**
     * @param Iterator $iterator
     * @param string   $scenarioId
     *
     * @return Iterator
     */
    private function mergeWithScenario($iterator, $scenarioId): Iterator
    {
        $scenario = $this->scenarioDataSource->find($scenarioId);

        if (!$scenario) {
            return $iterator;
        }

        $data = iterator_to_array($iterator);

        foreach ((array) $scenario['modulesTransitions'] as $id => $transition) {
            if (isset($data[$id])) {
                /** @var Module $module */
                $module = $data[$id];
                if (isset($transition['stateAfterTransition']['enabled'])) {
                    $module->scenarioState['enabled'] = $transition['stateAfterTransition']['enabled'];
                }
                if (isset($transition['stateAfterTransition']['installed'])) {
                    $module->scenarioState['installed'] = $transition['stateAfterTransition']['installed'];
                }
            }
        }

        return new ArrayIterator($data);
    }
}
