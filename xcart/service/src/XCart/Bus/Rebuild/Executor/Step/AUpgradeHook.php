<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step;

use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\Execute\CheckPacks;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;

abstract class AUpgradeHook extends AHook
{
    /**
     * @param ScriptState $scriptState
     *
     * @return int
     */
    public function getProgressMax(ScriptState $scriptState): int
    {
        return $this->isParentStepCompleted($scriptState)
            ? count($this->getTransitions($scriptState))
            : count($this->filterScriptTransitions($scriptState->transitions));
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return bool
     */
    protected function isParentStepCompleted(ScriptState $scriptState): bool
    {
        return $scriptState->isStepCompleted(CheckPacks::class);
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return StepState
     */
    protected function getParentStepState(ScriptState $scriptState): ?StepState
    {
        return $scriptState->getCompletedStepState(CheckPacks::class);
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return array
     */
    protected function getTransitions(ScriptState $scriptState): array
    {
        $transitions = [];

        $parentStepState = $this->getParentStepState($scriptState);
        if ($parentStepState) {
            $transitions = $parentStepState->finishedTransitions ?: [];
        }

        if (empty($transitions)) {
            return [];
        }

        return array_filter(array_map(
            function ($transition) {
                $hooks = $this->getHooksListByTransition($transition);
                if ($hooks) {
                    return [
                        'id'             => $transition['id'],
                        'version_before' => $transition['version_before'],
                        'version_after'  => $transition['version_after'],
                        'remain_hooks'   => $this->sortHooks($hooks),
                        'pack_dir'       => $transition['pack_dir'],
                    ];
                }

                return [];
            },
            $this->filterScriptTransitions($transitions)
        ));
    }

    /**
     * @param array $transition
     *
     * @return array
     */
    protected function getHooksListByTransition(array $transition): array
    {
        return $this->hookFilter->filterHooksByType(
            $transition['new_files'],
            $this->getType(),
            $transition['id'],
            $transition['version_before'],
            $transition['version_after']
        );
    }

    /**
     * @param array $list
     *
     * @return array
     */
    protected function sortHooks(array $list): array
    {
        return $this->hookFilter->sortAscending($list);
    }

    /**
     * @param array[] $transitions
     *
     * @return array[]
     */
    protected function filterScriptTransitions(array $transitions): array
    {
        return array_filter($transitions, static function ($transition) {
            return ChangeUnitProcessor::TRANSITION_UPGRADE === $transition['transition'];
        });
    }
}
