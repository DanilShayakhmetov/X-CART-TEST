<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Exception;

use Exception;

class ScriptExecutionError extends Exception
{
    const REASON_FINISHED_ALREADY      = 1;
    const REASON_CANCELED              = 2;
    const REASON_OTHER_IN_PROGRESS     = 3;
    const REASON_OWNED_BY_ANOTHER_USER = 4;
    const REASON_UNKNOWN_STEP          = 5;
    const REASON_MISSING_STEP_STATE    = 6;
    const REASON_CANNOT_ROLLBACK       = 7;
    const REASON_UNKNOWN_SCRIPT        = 8;

    /**
     * @param string $type
     *
     * @return static
     */
    public static function fromUnknownScript($type): ScriptExecutionError
    {
        return new static(sprintf('Unknown script "%s"', $type), self::REASON_UNKNOWN_SCRIPT);
    }

    /**
     * @return static
     */
    public static function fromNotOwnedProcess(): ScriptExecutionError
    {
        return new static('The process is owned by another user', self::REASON_OWNED_BY_ANOTHER_USER);
    }

    /**
     * @return static
     */
    public static function fromUnacceptableStateExecution(): ScriptExecutionError
    {
        return new static('This script state cannot be executed further', self::REASON_FINISHED_ALREADY);
    }

    /**
     * @return static
     */
    public static function fromFinishedProcess(): ScriptExecutionError
    {
        return new static('This script state cannot be executed further', self::REASON_FINISHED_ALREADY);
    }

    /**
     * @return static
     */
    public static function fromLockedState(): ScriptExecutionError
    {
        return new static('Can\'t start redeploy because other process is in progress', self::REASON_OTHER_IN_PROGRESS);
    }

    /**
     * @param int $index
     *
     * @return static
     */
    public static function fromUnknownStep($index): ScriptExecutionError
    {
        return new static(sprintf('Unknown step #%s', $index), self::REASON_UNKNOWN_STEP);
    }
}
