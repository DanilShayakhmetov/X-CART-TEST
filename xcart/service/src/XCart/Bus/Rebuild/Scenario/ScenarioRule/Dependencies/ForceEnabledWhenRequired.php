<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\ScenarioRule\Dependencies;

use XCart\Bus\Domain\Module;
use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;
use XCart\Bus\Rebuild\Scenario\ScenarioBuilder;
use XCart\Bus\Rebuild\Scenario\ScenarioRule\ScenarioRuleException;
use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * Check if module is required for others on disable transition
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 */
class ForceEnabledWhenRequired extends DependencyRuleAbstract
{
    /**
     * @param TransitionInterface $transition
     *
     * @return bool
     */
    public function isApplicable(TransitionInterface $transition): bool
    {
        return ($transition->getType() === ChangeUnitProcessor::TRANSITION_DISABLE
                || $transition->getType() === ChangeUnitProcessor::TRANSITION_REMOVE
            ) && $this->getRequiringModules($transition->getModuleId());
    }

    /**
     * @param TransitionInterface $transition
     * @param ScenarioBuilder     $scenarioBuilder
     *
     * @throws ScenarioRuleException
     */
    public function applyFilter(TransitionInterface $transition, ScenarioBuilder $scenarioBuilder): void
    {
        $requiring = $this->getRequiringModules($transition->getModuleId());

        foreach ($requiring as $requiringModule) {
            $requiringTransition = $scenarioBuilder->getTransition($requiringModule->id);

            if (!$requiringTransition) {
                $result = $requiringModule->enabled;

            } else {
                $after = $requiringTransition->getStateAfterTransition(
                    $requiringModule->toArray()
                );

                $result = $after['enabled'] === true;
            }

            if ($result) {
                throw ScenarioRuleException::fromDependenciesForceEnabledWhenRequired($transition->getModuleId(), $requiringModule->id);
            }
        }
    }

    /**
     * @param TransitionInterface $transition
     * @param ScenarioBuilder     $scenarioBuilder
     */
    public function applyTransform(TransitionInterface $transition, ScenarioBuilder $scenarioBuilder): void
    {
    }

    /**
     * @param string $id
     *
     * @return Module[]
     */
    protected function getRequiringModules($id): array
    {
        $modules = $this->installedModulesDataSource->getAll();

        return array_filter($modules, function ($module) use ($id) {
            /** @var Module $module */
            foreach ((array) $module->dependsOn as $dependency) {
                if ($this->getDependencyId($dependency) === $id) {
                    return true;
                }
            }

            return false;
        });
    }
}
