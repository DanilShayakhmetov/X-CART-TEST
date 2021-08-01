<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step;

use Exception;
use XCart\Bus\Exception\RebuildException;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\StepState;

interface StepInterface
{
    public const ACTION_EXECUTE   = 'execute';
    public const ACTION_RETRY     = 'retry';
    public const ACTION_IGNORE    = 'ignore';
    public const ACTION_RELEASE   = 'release';
    public const ACTION_SKIP_STEP = 'skip-step';

    /**
     * @param ScriptState $scriptState
     *
     * @return int
     */
    public function getProgressMax(ScriptState $scriptState): int;

    /**
     * Generate fresh StepState for correct execution
     *
     * @param ScriptState $scriptState
     * @param StepState   $stepState
     *
     * @return StepState
     */
    public function initialize(ScriptState $scriptState, StepState $stepState = null): StepState;

    /**
     * @param StepState $state Cloned state
     * @param string    $action
     * @param array     $params
     *
     * @return StepState
     *
     * @throws RebuildException
     * @throws Exception
     */
    public function execute(StepState $state, $action = self::ACTION_EXECUTE, array $params = []): StepState;
}
