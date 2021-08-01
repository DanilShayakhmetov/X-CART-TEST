<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\ScenarioRule\Dependencies;

use XCart\Bus\Rebuild\Scenario\ScenarioBuilder;
use XCart\Bus\Rebuild\Scenario\ScenarioRule\ScenarioRuleException;
use XCart\Bus\Rebuild\Scenario\Transition\DisableTransition;
use XCart\Bus\Rebuild\Scenario\Transition\EnableTransition;
use XCart\Bus\Rebuild\Scenario\Transition\InstallEnabledTransition;
use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;
use XCart\Bus\Rebuild\Scenario\Transition\UpgradeTransition;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * Enable required modules
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 */
class EnableNotEnabled extends DependencyRuleAbstract
{
    /**
     * @param TransitionInterface $transition
     *
     * @return bool
     */
    public function isApplicable(TransitionInterface $transition): bool
    {
        return parent::isApplicable($transition)
            && ($transition instanceof EnableTransition
                || $transition instanceof InstallEnabledTransition);
    }

    /**
     * @param TransitionInterface $transition
     * @param ScenarioBuilder     $scenarioBuilder
     *
     * @throws ScenarioRuleException
     */
    public function applyTransform(TransitionInterface $transition, ScenarioBuilder $scenarioBuilder): void
    {
        $dependencies = $this->getDependencies($transition);

        foreach ($dependencies as $dependency) {
            $id = $this->getDependencyId($dependency);

            if ($this->removeComplimentaryTransition($id, $scenarioBuilder)) {
                continue;
            }

            $installedDependency = $this->getInstalledModule($id);
            if ($installedDependency && $installedDependency->enabled === false) {
                $newTransition = new EnableTransition($id);
                $this->fillTransitionInfo($newTransition);

                $scenarioBuilder->addTransition($newTransition);
            }
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
     * @param string          $id
     * @param ScenarioBuilder $scenarioBuilder
     *
     * @return bool
     * @throws ScenarioRuleException
     */
    private function removeComplimentaryTransition($id, ScenarioBuilder $scenarioBuilder): bool
    {
        foreach ($scenarioBuilder->getTransitions() as $scenarioTransition) {
            if ($scenarioTransition instanceof DisableTransition
                && $scenarioTransition->getModuleId() === $id
            ) {
                $scenarioBuilder->removeTransition($id);

                return true;
            }
        }

        return false;
    }
}
