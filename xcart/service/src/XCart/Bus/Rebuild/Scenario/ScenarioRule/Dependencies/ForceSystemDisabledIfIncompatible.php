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
use XCart\Bus\Rebuild\Scenario\Transition\DisableTransition;
use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * Check if module is cannot be enabled in incompatible module enabled
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 */
class ForceSystemDisabledIfIncompatible extends DependencyRuleAbstract
{
    /**
     * @param TransitionInterface $transition
     *
     * @return bool
     */
    public function isApplicable(TransitionInterface $transition): bool
    {
        $types = [ChangeUnitProcessor::TRANSITION_ENABLE, ChangeUnitProcessor::TRANSITION_INSTALL_ENABLED];

        $result = in_array($transition->getType(), $types, true)
            && $this->getIncompatibleSystemModules($transition->getModuleId());

        return $result;
    }

    /**
     * @param TransitionInterface $transition
     * @param ScenarioBuilder     $scenarioBuilder
     *
     * @throws ScenarioRuleException
     */
    public function applyTransform(TransitionInterface $transition, ScenarioBuilder $scenarioBuilder): void
    {
        $incompatibleModules = $this->getIncompatibleSystemModules($transition->getModuleId());

        foreach ($incompatibleModules as $incompatibleModule) {
            $incompatibleTransition = $scenarioBuilder->getTransition(
                $incompatibleModule->id
            );

            if (!$incompatibleTransition) {
                $isGoingToBeEnabled = $incompatibleModule->enabled;

            } else {
                $after = $incompatibleTransition->getStateAfterTransition(
                    $incompatibleModule->toArray()
                );

                $isGoingToBeEnabled = $after['enabled'] === true;
            }

            if ($isGoingToBeEnabled) {
                $scenarioBuilder->removeTransition($incompatibleModule->id);

                $transition = $scenarioBuilder->fillSystemTransitionInfo(
                    new DisableTransition($incompatibleModule->id)
                );
                $scenarioBuilder->addTransition($transition);
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
     * @param string $id
     *
     * @return Module[]
     */
    protected function getIncompatibleSystemModules($id): array
    {
        $incompatibleModules = $this->getIncompatibleModules($id);

        return array_filter($incompatibleModules, function ($incompatibleModule) {
            /** @var Module $incompatibleModule */
            return $incompatibleModule->isSystem === true;
        });
    }
}
