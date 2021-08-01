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
 * Check if module is cannot be enabled in incompatible module enabled
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 */
class ForceDisabledWhenIncompatible extends DependencyRuleAbstract
{
    /**
     * @param TransitionInterface $transition
     *
     * @return bool
     */
    public function isApplicable(TransitionInterface $transition): bool
    {
        $types = [ChangeUnitProcessor::TRANSITION_ENABLE, ChangeUnitProcessor::TRANSITION_INSTALL_ENABLED];

        return in_array($transition->getType(), $types, true)
            && $this->getIncompatibleModules($transition->getModuleId());
    }

    /**
     * @param TransitionInterface $transition
     * @param ScenarioBuilder     $scenarioBuilder
     *
     * @throws ScenarioRuleException
     */
    public function applyFilter(TransitionInterface $transition, ScenarioBuilder $scenarioBuilder): void
    {
        $incompatibleModules = $this->getIncompatibleModules($transition->getModuleId());

        foreach ($incompatibleModules as $incompatibleModule) {
            $incompatibleTransition = $scenarioBuilder->getTransition(
                $incompatibleModule->id
            );

            if (!$incompatibleTransition) {
                $result = $incompatibleModule->enabled;

            } else {
                $after = $incompatibleTransition->getStateAfterTransition(
                    $incompatibleModule->toArray()
                );

                $result = $after['enabled'] === true;
            }

            if ($result) {
                throw ScenarioRuleException::fromDependenciesForceDisabledWhenIncompatible($transition->getModuleId(), $incompatibleModule->id);
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
    protected function getIncompatibleModules($id): array
    {
        /** @var Module[] $modules */
        $modules = $this->installedModulesDataSource->getAll();

        return array_filter($modules, function ($module) use ($id) {
            /** @var Module $module */
            $incompatibleModules = (array) $module->incompatibleWith;

            foreach ($incompatibleModules as $incompatibleModule) {
                if ($this->getDependencyId($incompatibleModule) === $id) {
                    return true;
                }
            }

            return false;
        });
    }
}
