<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\ScenarioRule\Dependencies;

use XCart\Bus\Domain\Module;
use XCart\Bus\Rebuild\Scenario\ScenarioBuilder;
use XCart\Bus\Rebuild\Scenario\ScenarioRule\ScenarioRuleException;
use XCart\Bus\Rebuild\Scenario\Transition\DisableTransition;
use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 */
class DisableNonRelatedSkins extends DependencyRuleAbstract
{
    /**
     * @param TransitionInterface $transition
     *
     * @return bool
     */
    public function isApplicable(TransitionInterface $transition): bool
    {
        $module     = $this->getInstalledModule($transition->getModuleId());
        $stateAfter = $transition->getStateAfterTransition($module ? $module->toArray() : []);

        return $module && $module->type === 'skin' && $stateAfter['enabled'] === true;
    }

    /**
     * @param TransitionInterface $transition
     * @param ScenarioBuilder     $scenarioBuilder
     *
     * @throws ScenarioRuleException
     */
    public function applyTransform(TransitionInterface $transition, ScenarioBuilder $scenarioBuilder): void
    {
        $excepted   = $this->getDependencyChain($transition->getModuleId());
        $excepted   = array_merge($excepted, $this->getRequiringSkins($transition->getModuleId()));
        $excepted[] = $transition->getModuleId();

        foreach ($this->getNonRelatedSkins($excepted) as $module) {
            $newTransition = new DisableTransition($module->id);
            $this->fillTransitionInfo($newTransition);

            $scenarioBuilder->addTransition($newTransition);
        }
    }

    /**
     * @param TransitionInterface $transition
     * @param ScenarioBuilder     $scenarioBuilder
     */
    public function applyFilter(TransitionInterface $transition, ScenarioBuilder $scenarioBuilder): void
    {
    }

    /**
     * @param string $moduleId
     *
     * @return string[]
     */
    private function getDependencyChain($moduleId): array
    {
        $dependencies = $this->getInstalledDependencies($moduleId);

        if (count($dependencies) > 0) {
            $childDeps = array_reduce($dependencies, function ($acc, $dependency) {
                $id   = $this->getDependencyId($dependency);
                $deps = $this->getDependencyChain($id);

                return array_merge($acc, $deps);
            }, []);

            $dependencies = array_map(function ($dependency) {
                return $this->getDependencyId($dependency);
            }, $dependencies);

            return array_unique(array_merge($dependencies, $childDeps));
        }

        return [];
    }

    /**
     * @param string $id
     *
     * @return Module[]
     */
    private function getRequiringSkins($id): array
    {
        $modules = $this->installedModulesDataSource->getAll();

        $requiringModules = array_filter($modules, function ($module) use ($id) {
            if ($module->type !== 'skin') {
                return false;
            }

            /** @var Module $module */
            foreach ((array) $module->dependsOn as $dependency) {
                if ($this->getDependencyId($dependency) === $id) {
                    return true;
                }
            }

            return false;
        });

        return array_map(function ($module) {
            return $module->id;
        }, $requiringModules);
    }

    /**
     * @param array $except
     *
     * @return Module[]
     */
    private function getNonRelatedSkins($except): array
    {
        /** @var Module[] $modules */
        $modules = $this->installedModulesDataSource->getAll();

        return array_filter($modules, function ($module) use ($except) {
            /** @var Module $module */
            return $module->enabled
                && $module->type === 'skin'
                && !in_array($module->id, $except, true);
        });
    }
}
