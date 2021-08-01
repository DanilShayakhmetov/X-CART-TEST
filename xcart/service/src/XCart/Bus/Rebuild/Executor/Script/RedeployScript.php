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
 * common steps order:
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\DownloadPacks
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\UnpackPacks
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\CheckFS
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\CheckPacks
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\PreUpgradeNote
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\DisableHook
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\RemoveHook
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\PreUpgradeHook
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\MovePacks
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\PostUpgradeHook
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\UpdateScriptState
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\UpdateModulesList
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\XCartStep
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\RemoveModules
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\UpdateDataSource
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\InstallHook
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\EnableHook
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\UpgradeAction
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\RebuildHook
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\PostRebuildHook
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\XCartStepFinalize
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\PostUpgradeNote
 * @see \XCart\Bus\Rebuild\Executor\Step\Execute\EditionChange
 *
 * @Service\Factory(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildScript(name="redeploy")
 */
class RedeployScript extends AScript
{
    /**
     * Initializes script execution and constructs the required script state
     *
     * @param string $id
     * @param array  $transitions
     *
     * @return ScriptState
     * @throws ScriptExecutionError
     */
    public function initializeByTransitions($id, array $transitions): ScriptState
    {
        $transitions = $this->transitionFilter->sortAscending($transitions);

        $state = new ScriptState([
            'id'             => $id,
            'type'           => 'redeploy',
            'canRollback'    => !empty($transitions),
            'transitions'    => $transitions,
            'state'          => ScriptState::STATE_INITIALIZED,
            'stepsCount'     => count($this->getSteps()),
            'currentStep'    => -1,
            'completedSteps' => [],
            'errors'         => [],
        ]);

        $state->currentStep = $this->getNextStepIndex($state);

        $step             = $this->getStepInstance($state->currentStep);
        $state->stepState = $step->initialize($state);

        $state->updateInfo();

        $state->progressMax   = $this->calculateRemainProgressMax($state);
        $state->progressValue = 0;

        $this->backup->create($id);

        $this->lockScript($state);

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
        return $scriptState->type === 'redeploy' && $scriptState->isExecutable();
    }
}
