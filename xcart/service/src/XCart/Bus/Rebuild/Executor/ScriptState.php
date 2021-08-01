<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor;

use XCart\Bus\Domain\PropertyBag;

/**
 * @property string      $id                 State identifier (generally is equal to generating scenario id)
 * @property string      $token              Owner user token
 * @property string      $returnUrl          (optional) Where to redirect after script execution
 * @property string      $failureReturnUrl   (optional) Where to redirect after script execution
 * @property array       $transitions        Scenario module transitions
 *
 * @property string      $errorType          Error code
 * @property string      $errorData          Additional error data
 * @property string      $errorTitle         General error message
 * @property string      $errorDescription   Detailed error description and steps to resolving the problem
 *
 * @property bool        $rollbackRequired   True if rollback is required to abort the script
 * @property int         $stepsCount         Steps count
 * @property int         $currentStep        Rebuild current step
 * @property string      $state              Script state
 * @property string      $type               Script type
 * @property string      $reason             Reason (install, redeploy, module-state, upgrade)*
 * @property bool        $canRollback        Rollback availability flag
 * @property StepState   $stepState          Current step state
 * @property StepState[] $completedSteps     Completed steps state
 * @property int         $lastModifiedTime   Script last modified timestamp
 * @property int         $initializedTime    Script initialized timestamp
 * @property int         $progressMax        Progress bar max value
 * @property int         $progressValue      Progress bar current value
 * @property ScriptState $parentState        Parent script state (used for rollback)
 * @property array       $storeMetadata      Abstract store metadata
 *
 * @property array       $currentStepInfo
 * @property array       $finishedStepInfo
 */
class ScriptState extends PropertyBag
{
    public const STATE_INITIALIZED           = 'initialized';
    public const STATE_IN_PROGRESS           = 'in_progress';
    public const STATE_FINISHED_SUCCESSFULLY = 'success';
    public const STATE_HELD                  = 'held';
    public const STATE_ERROR_ABORTED         = 'aborted';
    public const STATE_CANCELED              = 'canceled';

    /**
     * @return int
     */
    public function getNextStepIndex(): int
    {
        return $this->currentStep + 1;
    }

    /**
     * @param string $title
     * @param string $type
     * @param array  $data
     * @param string $description
     *
     * @return static
     */
    public function abort($title, $type = 'rebuild-dialog', array $data = [], $description = '')
    {
        $this->state = static::STATE_ERROR_ABORTED;

        $this->errorType        = $type;
        $this->errorTitle       = $title;
        $this->errorDescription = $description;
        $this->errorData        = json_encode($data);

        return $this;
    }

    /**
     * Checks if script is in the ending state
     *
     * @return bool
     */
    public function isInTheEnd()
    {
        return in_array($this->state, [
            self::STATE_ERROR_ABORTED,
            self::STATE_CANCELED,
            self::STATE_FINISHED_SUCCESSFULLY,
        ], true);
    }

    /**
     * Checks if this script state can be executed
     *
     * @return bool
     */
    public function isExecutable(): bool
    {
        $isCurrentStepOverlapped = $this->currentStep > $this->stepsCount;

        $isCurrentStepTheLastAndFinished = $this->stepState
            && $this->currentStep === $this->stepsCount
            && $this->stepState->isFinished();

        return !$this->isInTheEnd()
            && !$isCurrentStepOverlapped
            && !$isCurrentStepTheLastAndFinished;
    }

    /**
     * @param string $token
     */
    public function touch($token): void
    {
        if (empty($this->initializedTime)) {
            $this->initializedTime = time();
        }

        $this->lastModifiedTime = time();
        $this->token            = $token;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function isStepCompleted($id)
    {
        /** @var StepState $stepState */
        foreach ((array) $this->completedSteps as $stepState) {
            if (empty($stepState->id)) {
                continue;
            }

            if ($stepState->id === $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $id
     *
     * @return StepState|null
     */
    public function getCompletedStepState($id): ?StepState
    {
        /** @var StepState $stepState */
        foreach ((array) $this->completedSteps as $stepState) {
            if (empty($stepState->id)) {
                continue;
            }

            if ($stepState->id === $id) {
                return $stepState;
            }
        }

        return null;
    }

    /**
     * @return int
     */
    public function getCompletedStepsProgressMax()
    {
        $result = 0;

        /** @var StepState $stepState */
        foreach ((array) $this->completedSteps as $stepState) {
            $result += $stepState->progressMax;
        }

        return $result;
    }

    public function updateInfo()
    {
        $currentStepInfo = [];

        $finishedStepInfo = array_merge(... array_map(
            function ($item) {
                /** @var StepState $item */
                return $item->finishedActionInfo ?: [];
            },
            $this->completedSteps
        ) ?: [[]]);

        if ($this->stepState) {
            $currentStepInfo  = $this->stepState->currentActionInfo ?: [];
            $finishedStepInfo = array_merge($finishedStepInfo, $this->stepState->finishedActionInfo ?: []);
        }

        foreach ($currentStepInfo as $k => $info) {
            $currentStepInfo[$k]['params'] = json_encode($currentStepInfo[$k]['params'] ?? []);
        }

        foreach ($finishedStepInfo as $k => $info) {
            $finishedStepInfo[$k]['params'] = json_encode($finishedStepInfo[$k]['params'] ?? []);
        }

        $this->currentStepInfo  = array_reverse($currentStepInfo);
        $this->finishedStepInfo = array_reverse($finishedStepInfo);
    }
}
