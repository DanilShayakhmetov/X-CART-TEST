<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\ScenarioRule\Dependencies;

use XCart\Bus\Rebuild\Scenario\ScenarioBuilder;
use XCart\Bus\Rebuild\Scenario\ScenarioRule\ScenarioRuleException;
use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;
use XCart\Bus\Rebuild\Scenario\Transition\UpgradeTransition;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 */
class UpgradeDependency extends DependencyRuleAbstract
{
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

            $installedDependency   = $this->getInstalledModule($id);
            $marketplaceDependency = $this->getMarketplaceModule($id);

            if ($installedDependency
                && $marketplaceDependency
                && isset($dependency['minVersion'])
                && version_compare($installedDependency->version, $dependency['minVersion'], '<')
            ) {
                $newTransition = new UpgradeTransition(
                    $dependency['id'],
                    $marketplaceDependency->version
                );
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
        //$dependencies = $this->getDependencies($transition);
        //
        //foreach ($dependencies as $dependency) {
        //    $id                  = $this->getDependencyId($dependency);
        //    $installedDependency = $this->getInstalledModule($id);
        //
        //    if ($installedDependency
        //        && isset($dependency['maxVersion'])
        //        && version_compare($installedDependency['version'], $dependency['maxVersion'], '>')
        //    ) {
        //        throw new ScenarioTransitionFailed(
        //            $transition->getModuleId(),
        //            $installedDependency['id'],
        //            'upgrade',
        //            ''
        //        );
        //    }
        //}
    }
}
