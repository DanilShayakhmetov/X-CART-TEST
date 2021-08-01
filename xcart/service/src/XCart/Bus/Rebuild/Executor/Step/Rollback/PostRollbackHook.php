<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Rollback;

use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\AUpgradeHook;
use XCart\Bus\Rebuild\Executor\Step\Execute\PreUpgradeHook;
use XCart\Bus\Rebuild\Executor\Step\Execute\MovePacks;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "rollback", weight = "9000")
 */
class PostRollbackHook extends AUpgradeHook
{
    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'post_rollback';
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return int
     */
    public function getProgressMax(ScriptState $scriptState): int
    {
        return $this->isParentStepCompleted($scriptState)
            ? count($this->getTransitions($scriptState))
            : 0;
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return string|null
     */
    protected function getCacheId(ScriptState $scriptState): ?string
    {
        $parentStepState = $scriptState->getCompletedStepState(UpdateModulesList::class);

        return $parentStepState ? $parentStepState->data['cacheId'] : null;
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return StepState
     */
    protected function getParentStepState(ScriptState $scriptState): ?StepState
    {
        return parent::getParentStepState($scriptState->parentState);
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return bool
     */
    protected function isParentStepCompleted(ScriptState $scriptState): bool
    {
        $parentStepState = $scriptState->parentState->stepState;
        $isStepInitialized = $parentStepState
            && !empty($parentStepState->id)
            && in_array($parentStepState->id, [PreUpgradeHook::class, MovePacks::class]);

        return parent::isParentStepCompleted($scriptState->parentState)
            && ($scriptState->parentState->isStepCompleted(PreUpgradeHook::class)
                || $scriptState->parentState->isStepCompleted(MovePacks::class)
                || $isStepInitialized
            );
    }

    /**
     * @param array $list
     *
     * @return array
     */
    protected function sortHooks(array $list): array
    {
        return $this->hookFilter->sortDescending($list);
    }
}
