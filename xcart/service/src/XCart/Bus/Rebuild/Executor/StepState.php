<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor;

use XCart\Bus\Domain\PropertyBag;

/**
 * @property string   $id                  Step ID (usually class name)
 * @property integer  $index               Step index
 * @property string   $state               Step state
 * @property string   $rebuildId           Scenario id
 * @property array    $remainTransitions   Transitions to execute
 * @property array    $finishedTransitions Executed transitions
 * @property array    $currentActionInfo   Step current action info
 * @property array    $finishedActionInfo  Step finished action(s) info
 * @property string[] $errors              Step errors
 * @property int      $progressMax         Progress bar max value
 * @property int      $progressValue       Progress bar current value
 * @property mixed    $data                Some data
 */
class StepState extends PropertyBag
{
    public const STATE_INITIALIZED           = 'initialized';
    public const STATE_IN_PROGRESS           = 'in_progress';
    public const STATE_FINISHED_SUCCESSFULLY = 'success'; // @todo: rename
    public const STATE_HELD                  = 'held';
    public const STATE_ABORTED               = 'aborted';

    /**
     * @return bool
     */
    public function isFinished()
    {
        return self::STATE_FINISHED_SUCCESSFULLY === $this->state;
    }
}
