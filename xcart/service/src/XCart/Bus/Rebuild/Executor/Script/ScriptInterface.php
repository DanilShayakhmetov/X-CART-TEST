<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Script;

use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;

interface ScriptInterface
{
    /**
     * @param array $steps
     */
    public function setSteps(array $steps): void;

    /**
     * @return StepInterface[]
     */
    public function getSteps(): array;

    /**
     * Initializes script execution and constructs the required script state
     *
     * @param string $id
     * @param array  $transitions
     *
     * @return ScriptState
     */
    public function initializeByTransitions($id, array $transitions): ScriptState;

    /**
     * Initializes script execution and constructs the required script state
     *
     * @param string      $id
     * @param ScriptState $parentScriptState
     *
     * @return ScriptState
     */
    public function initializeByState($id, ScriptState $parentScriptState): ScriptState;

    /**
     * Checks if given script state is sufficient and consistent for this script
     *
     * @param ScriptState $scriptState
     *
     * @return bool
     */
    public function canAcceptState(ScriptState $scriptState): bool;

    /**
     * Checks if this script can be executed by other user
     *
     * @return bool
     */
    public function isOwnerLocked(): bool;

    /**
     * Executes the script in given script state
     *
     * @param ScriptState $scriptState
     * @param string      $action
     * @param array       $params
     *
     * @return ScriptState
     */
    public function execute(ScriptState $scriptState, $action = StepInterface::ACTION_EXECUTE, array $params = []): ScriptState;

    /**
     * Cancels the script in given script state
     *
     * @param ScriptState $scriptState
     *
     * @return ScriptState
     */
    public function cancel(ScriptState $scriptState);
}
