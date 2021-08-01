<?php

namespace XCart\Bus\Rebuild\Executor\Script;

use XCart\Bus\Exception\ScriptExecutionError;
use XCart\Bus\Rebuild\Executor\RebuildLockManager;
use XCart\Bus\Rebuild\Executor\ScriptState;

trait BlockingTrait
{
    /**
     * @var RebuildLockManager
     */
    protected $lockManager;

    /**
     * Initializes script execution and constructs the required script state
     *
     * @param ScriptState $state
     *
     * @return ScriptState
     * @throws ScriptExecutionError
     */
    public function lockScript(ScriptState $state)
    {
        if ($this->lockManager->isAnyRebuildStartedFlagSet()) {
            throw ScriptExecutionError::fromLockedState();
        }

        $this->lockManager->setRebuildStartedFlag($state->id);

        return $state;
    }

    /**
     * Unlocks the script
     *
     * @param ScriptState $state
     */
    public function unlockScript(ScriptState $state): void
    {
        $this->lockManager->unsetRebuildStartedFlag($state->id);
    }
}
