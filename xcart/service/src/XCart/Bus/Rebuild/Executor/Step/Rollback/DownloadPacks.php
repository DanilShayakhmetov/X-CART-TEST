<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Rollback;

use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\Execute\DownloadPacks as ExecuteDownloadPacks;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\Bus\System\FilesystemInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service
 * @RebuildStep(script = "rollback", weight = "12000")
 * @RebuildStep(script = "self-rollback", weight = "4000")
 */
class DownloadPacks implements StepInterface
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var bool
     */
    private $removePacks = true;

    /**
     * @param FilesystemInterface $filesystem
     */
    public function __construct(
        FilesystemInterface $filesystem
    ) {
        $this->filesystem = $filesystem;
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return int
     */
    public function getProgressMax(ScriptState $scriptState): int
    {
        return count($this->getTransitions($scriptState));
    }

    /**
     * @param ScriptState $scriptState
     * @param StepState   $stepState
     *
     * @return StepState
     */
    public function initialize(ScriptState $scriptState, StepState $stepState = null): StepState
    {
        $state = new StepState([
            'id'                  => static::class,
            'state'               => StepState::STATE_INITIALIZED,
            'rebuildId'           => $scriptState->id,
            'remainTransitions'   => $this->getTransitions($scriptState),
            'finishedTransitions' => [],
            'progressMax'         => $this->getProgressMax($scriptState),
            'progressValue'       => 0,
        ]);

        $state->currentActionInfo = $this->getCurrentActionInfoMessage($state);

        return $state;
    }

    /**
     * @param StepState $state
     * @param string    $action
     * @param array     $params
     *
     * @return StepState
     */
    public function execute(StepState $state, $action = self::ACTION_EXECUTE, array $params = []): StepState
    {
        $state = $this->processCurrentTransition($state);

        $state->state = !empty($state->remainTransitions)
            ? StepState::STATE_IN_PROGRESS
            : StepState::STATE_FINISHED_SUCCESSFULLY;

        $state->currentActionInfo  = $this->getCurrentActionInfoMessage($state);
        $state->finishedActionInfo = $this->getFinishedActionInfoMessage($state);

        return $state;
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return array[]
     */
    private function getTransitions(ScriptState $scriptState): array
    {
        if (!$this->removePacks) {
            return [];
        }

        $parentScriptState = $scriptState->parentState;
        $parentStepState   = $parentScriptState->stepState->id === ExecuteDownloadPacks::class
            ? $parentScriptState->stepState
            : $parentScriptState->getCompletedStepState(ExecuteDownloadPacks::class);

        if (!$parentStepState) {
            return [];
        }

        $transitions = $parentStepState->finishedTransitions;

        if ($parentStepState->remainTransitions) {
            $currentTransition = current($parentStepState->remainTransitions);

            $transitions[$currentTransition['id']] = $currentTransition;
        }

        return array_reverse($transitions);
    }

    /**
     * @param StepState $state
     *
     * @return string[]
     */
    private function getCurrentActionInfoMessage(StepState $state): array
    {
        $finished = count($state->finishedTransitions);
        $total    = $finished + count($state->remainTransitions);

        return $total !== $finished
            ? [[
                   'message' => 'rollback.download_packs.state',
                   'params'  => [$finished, $total],
               ]]
            : [];
    }

    /**
     * @param StepState $state
     *
     * @return string[]
     */
    private function getFinishedActionInfoMessage(StepState $state): array
    {
        $finished = count($state->finishedTransitions);
        $total    = $finished + count($state->remainTransitions);

        return $total === $finished
            ? [[
                   'message' => 'rollback.download_packs.state.finished',
                   'params'  => [$finished, $total],
               ]]
            : [];
    }

    /**
     * @param StepState $state
     *
     * @return StepState
     */
    private function processCurrentTransition(StepState $state): StepState
    {
        $remainTransitions = $state->remainTransitions;

        if (empty($remainTransitions)) {
            return $state;
        }

        $finishedTransitions = $state->finishedTransitions;
        $transition          = current($state->remainTransitions);
        $id                  = $transition['id'];

        $progressValue = $state->progressValue;

        if (!empty($transition['pack_path'])) {
            $this->filesystem->remove($transition['pack_path']);
        }

        $finishedTransitions[$id] = $transition;
        unset($remainTransitions[$id]);
        $progressValue++;

        $state->remainTransitions   = $remainTransitions;
        $state->finishedTransitions = $finishedTransitions;
        $state->progressValue       = $progressValue;

        return $state;
    }
}
