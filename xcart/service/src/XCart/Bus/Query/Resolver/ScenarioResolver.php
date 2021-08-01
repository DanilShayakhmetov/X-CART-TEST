<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Resolver;

use Exception;
use GraphQL\Type\Definition\ResolveInfo;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\Bus\Domain\Module;
use XCart\Bus\Helper\UrlBuilder;
use XCart\Bus\Query\Context;
use XCart\Bus\Query\Data\Flatten\Flatten;
use XCart\Bus\Query\Data\ModulesDataSource;
use XCart\Bus\Query\Data\ScenarioDataSource;
use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class ScenarioResolver
{
    /**
     * @var ScenarioDataSource
     */
    private $scenarioDataSource;

    /**
     * @var ModulesDataSource
     */
    private $modulesDataSource;

    /**
     * @var ChangeUnitProcessor
     */
    private $changeUnitProcessor;

    /**
     * @var ModulesResolver
     */
    private $modulesResolver;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @param ScenarioDataSource  $scenarioDataSource
     * @param ModulesDataSource   $modulesDataSource
     * @param ChangeUnitProcessor $changeUnitProcessor
     * @param ModulesResolver     $modulesResolver
     * @param UrlBuilder          $urlBuilder
     */
    public function __construct(
        ScenarioDataSource $scenarioDataSource,
        ModulesDataSource $modulesDataSource,
        ChangeUnitProcessor $changeUnitProcessor,
        ModulesResolver $modulesResolver,
        UrlBuilder $urlBuilder
    ) {
        $this->scenarioDataSource  = $scenarioDataSource;
        $this->changeUnitProcessor = $changeUnitProcessor;
        $this->modulesDataSource   = $modulesDataSource;
        $this->modulesResolver     = $modulesResolver;
        $this->urlBuilder          = $urlBuilder;
    }

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
    public function find($value, $args, Context $context, ResolveInfo $info): array
    {
        $scenario = $this->scenarioDataSource->find($args['id']);

        if ($scenario) {
            return $this->changeUnitProcessor->process($scenario, $scenario['changeUnits'] ?? []);
        }

        return [];
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
    public function createScenario($value, $args, Context $context, ResolveInfo $info): array
    {
        if (!($context->mode === Context::ACCESS_MODE_FULL)) {
            return [];
        }

        $type      = $args['type'] ?? 'common';
        $returnUrl = isset($args['returnUrl']) && $this->urlBuilder->isSelfURL($args['returnUrl']) ? $args['returnUrl'] : null;

        $scenario = $this->scenarioDataSource->startEmptyScenario($type, $returnUrl);

        $result = $this->scenarioDataSource->saveOne($scenario);

        if ($result === false) {
            throw new Exception("Can't create scenario");
        }

        return $scenario;
    }

    /**
     * @param mixed       $value
     * @param array       $args
     * @param Context     $context
     * @param ResolveInfo $info
     *
     * @return string
     * @throws Exception
     *
     * @Resolver()
     */
    public function discardScenario($value, $args, Context $context, ResolveInfo $info): string
    {
        if (!($context->mode === Context::ACCESS_MODE_FULL)) {
            return '';
        }

        $scenarioId = $args['scenarioId'] ?? null;

        if (!$scenarioId) {
            throw new Exception('No scenario id given');
        }

        $result = $this->scenarioDataSource->removeOne($scenarioId);

        if ($result === false) {
            throw new Exception('No scenario found for id ' . $scenarioId);
        }

        return $scenarioId;
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
    public function changeModulesState($value, $args, Context $context, ResolveInfo $info): array
    {
        if (!($context->mode === Context::ACCESS_MODE_FULL)) {
            return [];
        }

        $scenarioId = $args['scenarioId'] ?? null;
        $scenario   = $this->scenarioDataSource->startScenario($scenarioId);
        if (!$scenario) {
            throw new Exception('No scenario found for id ' . ($scenarioId ?? '[empty]'));
        }

        $newScenario = $this->changeUnitProcessor->process($scenario, $args['states'] ?? []);

        $this->scenarioDataSource->saveOne($newScenario);

        return $newScenario;
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
    public function changeSkinState($value, $args, Context $context, ResolveInfo $info): array
    {
        if (!($context->mode === Context::ACCESS_MODE_FULL)) {
            return [];
        }

        $moduleId = $args['moduleId'];

        $type      = $args['type'] ?? 'common';

        $scenario = $this->changeSkin($this->scenarioDataSource->startEmptyScenario($type), $moduleId);

        $scenario['returnUrl'] = isset($args['returnUrl']) && $this->urlBuilder->isSelfURL($args['returnUrl'])
            ? $args['returnUrl'] . '&rebuildId=' . $scenario['id']
            : null;

        $this->scenarioDataSource->saveOne($scenario);

        return $scenario;
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
    public function mutateRemoveUnallowedModules($value, $args, Context $context, ResolveInfo $info): array
    {
        if (!($context->mode === Context::ACCESS_MODE_FULL)) {
            return [];
        }

        $unallowedModulesPage = $this->modulesResolver->resolvePage([], ['licensed' => false], $context, $info);
        /** @var Module[] $unallowedModules */
        $unallowedModules = $unallowedModulesPage['modules'] ?? [];

        $changeUnits = [];
        foreach ($unallowedModules as $module) {
            $changeUnits[] = [
                'id'     => $module->id,
                'remove' => true,
            ];
        }

        $scenario = $this->changeUnitProcessor->process($this->scenarioDataSource->startEmptyScenario(), $changeUnits);

        $this->scenarioDataSource->saveOne($scenario);

        return $scenario;
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
    public function mutateDisableUnallowedModules($value, $args, Context $context, ResolveInfo $info): array
    {
        if (!($context->mode === Context::ACCESS_MODE_FULL)) {
            return [];
        }

        $unallowedModulesPage = $this->modulesResolver->resolvePage(
            [],
            ['licensed' => false, 'enabled' => true],
            $context,
            $info
        );

        /** @var Module[] $unallowedModules */
        $unallowedModules = $unallowedModulesPage['modules'] ?? [];

        $changeUnits = [];
        foreach ($unallowedModules as $module) {
            $changeUnits[] = [
                'id'     => $module->id,
                'enable' => false,
            ];
        }

        $scenario = $this->changeUnitProcessor->process($this->scenarioDataSource->startEmptyScenario(), $changeUnits);

        $this->scenarioDataSource->saveOne($scenario);

        return $scenario;
    }

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
    public function resolveScenarioInfo($value, $args, Context $context, ResolveInfo $info): array
    {
        $module = $this->modulesResolver->getModule($value['id'], $context);

        return array_replace(
            $value['info'] ?? [],
            [
                'moduleId'      => $module ? $module->id : '',
                'moduleName'    => $module ? $module->moduleName : '',
                'moduleLicense' => (bool) ($module ? $module->hasLicense : 0),
            ]
        );
    }

    /**
     * @param array  $scenario
     * @param string $moduleId
     *
     * @return array|null
     * @throws Exception
     */
    private function changeSkin($scenario, $moduleId): array
    {
        $changeUnitsToDisable = $this->getDisableAllSkinsChangeUnits();
        $changeUnitsToEnable  = $moduleId !== 'standard'
            ? $this->getEnableSkinChangeUnits($moduleId)
            : [];

        $scenario = $this->changeUnitProcessor->process($scenario, $changeUnitsToDisable);
        $scenario = $this->changeUnitProcessor->process($scenario, $changeUnitsToEnable);

        return $scenario;
    }

    /**
     * @param string $moduleId
     *
     * @return array
     */
    private function getEnableSkinChangeUnits($moduleId): array
    {
        /** @var Module $module */
        $module = $this->modulesDataSource->findOne($moduleId, Flatten::RULE_LAST, [
            'type'      => 'skin',
            'installed' => true,
        ]);

        if ($module) {
            return [
                $module->id => [
                    'id'     => $module->id,
                    'enable' => true,
                ],
            ];
        }

        return [];
    }

    /**
     * @return array
     */
    private function getDisableAllSkinsChangeUnits(): array
    {
        $skins = $this->modulesDataSource->getSlice(Flatten::RULE_LAST, [
            'type'      => 'skin',
            'installed' => true,
            'enabled'   => 'enabled',
        ]);

        if ($skins) {
            $moduleIds = array_map(static function ($skin) {
                return $skin['id'];
            }, $skins);

            $_skins = [];
            foreach ($skins as $module) {
                if (array_intersect($module->dependsOn, $moduleIds)) {
                    array_unshift($_skins, $module);
                } else {
                    array_push($_skins, $module);
                }
            }

            return array_map(static function ($module) {
                return [
                    'id'     => $module['id'],
                    'enable' => false,
                ];
            }, $_skins);
        }

        return [];
    }
}
