<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Script;

use XCart\Bus\Core\Annotations\RebuildScript;
use XCart\Bus\Exception\ScriptExecutionError;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * common steps order
 * @see \XCart\Bus\Rebuild\Executor\Step\Rollback\RestoreFiles
 * @see \XCart\Bus\Rebuild\Executor\Step\Rollback\UpdateDataSource
 * @see \XCart\Bus\Rebuild\Executor\Step\Rollback\UnpackPacks
 * @see \XCart\Bus\Rebuild\Executor\Step\Rollback\DownloadPacks
 * @see \XCart\Bus\Rebuild\Executor\Step\Rollback\ReloadPage
 *
 * @Service\Factory(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildScript(name="self-rollback")
 */
class SelfRollbackScript extends AScript
{
    /**
     * Initializes script execution and constructs the required script state
     *
     * @param string      $id
     * @param ScriptState $parentScriptState
     *
     * @return ScriptState
     * @throws ScriptExecutionError
     */
    public function initializeByState($id, ScriptState $parentScriptState): ScriptState
    {
        $transitions = $this->transitionFilter->sortDescending($parentScriptState->transitions);

        $state = new ScriptState([
            'id'             => $id,
            'type'           => 'self-rollback',
            'reason'         => $parentScriptState->reason,
            'canRollback'    => false,
            'transitions'    => $transitions,
            'state'          => ScriptState::STATE_INITIALIZED,
            'stepsCount'     => count($this->getSteps()),
            'currentStep'    => -1,
            'completedSteps' => [],
            'errors'         => [],
            'parentState'    => $parentScriptState,
            'storeMetadata'  => $parentScriptState->storeMetadata,
        ]);

        $state->currentStep = $this->getNextStepIndex($state);
        $step               = $this->getStepInstance($state->currentStep);
        $state->stepState   = $step->initialize($state);

        $state->updateInfo();

        $state->progressMax   = $this->calculateRemainProgressMax($state);
        $state->progressValue = 0;

        if ($parentScriptState->state === ScriptState::STATE_FINISHED_SUCCESSFULLY) {
            $this->lockScript($state);
        }

        if ($this->filesystem->exists($this->packsDirLast)) {
            if ($this->filesystem->exists($this->packsDir)) {
                $this->filesystem->remove($this->packsDir);
            }

            $this->filesystem->rename(
                $this->packsDirLast,
                $this->packsDir
            );
        }

        return $state;
    }

    /**
     * Checks if given script state is sufficient and consistent for this script
     *
     * @param ScriptState $scriptState
     *
     * @return bool
     */
    public function canAcceptState(ScriptState $scriptState): bool
    {
        return $scriptState->type === 'self-rollback' && $scriptState->isExecutable();
    }
}
