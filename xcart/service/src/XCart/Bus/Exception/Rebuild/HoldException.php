<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Exception\Rebuild;

use XCart\Bus\Exception\RebuildException;
use XCart\Bus\Rebuild\Executor\StepState;

class HoldException extends RebuildException
{
    /**
     * @param StepState $state
     *
     * @return RebuildException
     */
    public static function fromCheckStepModifiedFilesPresent($state): RebuildException
    {
        $data = $state->data;

        return (new self('rebuild.modified-dialog.title'))
            ->setType('file-modification-dialog')
            ->setDescription('rebuild.modified-dialog.description')
            ->setData($data['modified'])
            ->setStepState($state);
    }

    /**
     * @param StepState $state
     *
     * @return RebuildException
     */
    public static function fromCheckPostponedHooksStepHooksPresent($state): RebuildException
    {
        $data = $state->data;

        return (new self('rebuild.postponed-hooks-dialog.title'))
            ->setType('postponed-hooks-dialog')
            ->setDescription('rebuild.postponed-hooks-dialog.description')
            ->setData($data['modulesWithHooks'])
            ->setStepState($state);
    }

    /**
     * @param StepState $state
     * @param string[]  $commands
     *
     * @return RebuildException
     */
    public static function fromCheckFSErrorsPresent($state, $commands): RebuildException
    {
        $data = $state->data;

        return (new self('rebuild.fs-errors-dialog.title'))
            ->setType('fs-errors-dialog')
            ->setDescription('rebuild.fs-errors-dialog.description')
            ->setData($commands)
            ->setStepState($state);
    }

    /**
     * @param string    $type
     * @param StepState $state
     *
     * @return RebuildException
     */
    public static function fromAUpgradeNoteStepNote($type, $state): RebuildException
    {
        return (new self('Upgrade note'))
            ->setType('note-' . $type)
            ->setData($state->data)
            ->setStepState($state);
    }

    /**
     * @param StepState $state
     *
     * @return RebuildException
     */
    public static function fromReloadPageStepReload($state): RebuildException
    {
        return (new self('Reload page'))
            ->setType('reload-page')
            ->setStepState($state);
    }
}
