<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Script;

use Psr\Log\LoggerInterface;
use Silex\Application;
use XCart\Bus\Domain\Backup\BackupInterface;
use XCart\Bus\Exception\Rebuild\HoldException;
use XCart\Bus\Exception\RebuildException;
use XCart\Bus\Exception\ScriptExecutionError;
use XCart\Bus\Helper\TransitionFilter;
use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\Bus\Rebuild\Executor\RebuildLockManager;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\Bus\System\FilesystemInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class AScript implements ScriptInterface
{
    use BlockingTrait;

    /**
     * @var StepInterface[]
     */
    protected $steps = [];

    /**
     * @var TransitionFilter
     */
    protected $transitionFilter;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var BackupInterface
     */
    protected $backup;

    /**
     * @var CoreConfigDataSource
     */
    private $coreConfigDataSource;

    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $packsDir;

    /**
     * @var string
     */
    protected $packsDirLast;

    /**
     * @param RebuildLockManager   $rebuildLockManager
     * @param CoreConfigDataSource $coreConfigDataSource
     * @param TransitionFilter     $transitionFilter ,
     * @param LoggerInterface      $logger
     * @param BackupInterface      $backup
     * @param FilesystemInterface  $filesystem
     * @param Application          $app
     */
    public function __construct(
        RebuildLockManager $rebuildLockManager,
        CoreConfigDataSource $coreConfigDataSource,
        TransitionFilter $transitionFilter,
        LoggerInterface $logger,
        BackupInterface $backup,
        FilesystemInterface $filesystem,
        Application $app
    ) {
        $this->lockManager          = $rebuildLockManager;
        $this->coreConfigDataSource = $coreConfigDataSource;
        $this->transitionFilter     = $transitionFilter;
        $this->logger               = $logger;
        $this->backup               = $backup;
        $this->filesystem           = $filesystem;
        $this->packsDir             = $app['config']['module_packs_dir'];
        $this->packsDirLast         = $app['config']['module_packs_dir_last'];
    }

    /**
     * @return StepInterface[]
     */
    public function getSteps(): array
    {
        return $this->steps;
    }

    /**
     * @param StepInterface[] $steps
     */
    public function setSteps(array $steps): void
    {
        $this->steps = $steps;
    }

    /**
     * Initializes script execution and constructs the required script state
     *
     * @param string $id
     * @param array  $transitions
     *
     * @return ScriptState
     */
    public function initializeByTransitions($id, array $transitions): ScriptState
    {
        return new ScriptState();
    }

    /**
     * Initializes script execution and constructs the required script state
     *
     * @param string      $id
     * @param ScriptState $parentScriptState
     *
     * @return ScriptState
     */
    public function initializeByState($id, ScriptState $parentScriptState): ScriptState
    {
        return new ScriptState();
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
        return $scriptState->isExecutable();
    }

    /**
     * @return bool
     */
    public function isOwnerLocked(): bool
    {
        return true;
    }

    /**
     * Executes script in given script state
     *
     * @param ScriptState $scriptState
     * @param string      $action
     * @param array       $params
     *
     * @return ScriptState
     */
    public function execute(ScriptState $scriptState, $action = StepInterface::ACTION_EXECUTE, array $params = []): ScriptState
    {
        // get step executor
        try {
            $step = $this->getStepInstance($scriptState->currentStep);

        } catch (ScriptExecutionError $e) {
            $this->logger->critical(sprintf('Script execution error: %s', $e->getMessage()));
            $this->logger->debug(
                'Script execution error',
                [
                    'scriptState' => $scriptState,
                ]
            );

            return $scriptState->abort($e->getMessage());
        }

        // for the first step only
        if (null === $scriptState->stepState) {
            $scriptState->stepState = $step->initialize($scriptState);
        }

        // execute step
        try {
            if ($scriptState->isStepCompleted(get_class($step))) {
                $stepState = $scriptState->getCompletedStepState(get_class($step));
            } else {
                $stepState = $step->execute(clone $scriptState->stepState, $action, $params);
            }
        } catch (RebuildException $e) {
            if ($e instanceof HoldException) {
                $stepState              = $e->getStepState();
                $scriptState->stepState = $stepState;

                $completedProgress          = $scriptState->getCompletedStepsProgressMax();
                $scriptState->progressMax   = $completedProgress + $this->calculateRemainProgressMax($scriptState);
                $scriptState->progressValue = $completedProgress + $stepState->progressValue;
            }

            return $scriptState->abort(
                $e->getMessage(),
                $e->getType(),
                $e->getData(),
                $e->getDescription()
            );

        } catch (\Throwable $e) {
            return $scriptState->abort($e->getMessage());
        }

        $completedProgress          = $scriptState->getCompletedStepsProgressMax();
        $scriptState->progressMax   = $completedProgress + $this->calculateRemainProgressMax($scriptState);
        $scriptState->progressValue = $completedProgress + $stepState->progressValue;

        // update state
        if ($stepState->state === StepState::STATE_FINISHED_SUCCESSFULLY) {
            if ($this->isNextStepAvailable($scriptState)) {
                $scriptState->completedSteps = array_merge(
                    $scriptState->completedSteps,
                    [$scriptState->currentStep => $stepState]
                );

                $scriptState->currentStep = $this->getNextStepIndex($scriptState);

                // initialize next step
                try {
                    $step = $this->getStepInstance($scriptState->currentStep);

                } catch (ScriptExecutionError $e) {
                    $this->logger->critical(sprintf('Script execution error: %s', $e->getMessage()));
                    $this->logger->debug(
                        sprintf('Script execution error'),
                        [
                            'scriptState' => $scriptState,
                        ]
                    );

                    return $scriptState->abort($e->getMessage());
                }

                $scriptState->stepState = $step->initialize($scriptState);

                // update script state
                $scriptState->state = ScriptState::STATE_IN_PROGRESS;

            } elseif (!$scriptState->errorMessage && !$scriptState->errors) {
                $scriptState->completedSteps = array_merge(
                    $scriptState->completedSteps,
                    [$scriptState->currentStep => $stepState]
                );

                $scriptState->stepState = $stepState;
                $scriptState->state     = ScriptState::STATE_FINISHED_SUCCESSFULLY;

                $this->coreConfigDataSource->saveOne(time(), 'cacheDate');

                if ($this->filesystem->exists($this->packsDirLast)) {
                    $this->filesystem->remove($this->packsDirLast);
                }

                if ($this->filesystem->exists($this->packsDir)) {
                    $this->filesystem->rename(
                        $this->packsDir,
                        $this->packsDirLast
                    );
                }
            }
        } else {
            $scriptState->stepState = $stepState;
            $scriptState->state     = ScriptState::STATE_IN_PROGRESS;
        }

        $scriptState->updateInfo();

        if ($this->canUnlock($scriptState)) {
            $this->unlockScript($scriptState);
        }

        return $scriptState;
    }

    /**
     * Executes script in given script state
     *
     * @param ScriptState $scriptState
     *
     * @return ScriptState
     */
    public function cancel(ScriptState $scriptState)
    {
        $scriptState->state = ScriptState::STATE_CANCELED;

        $this->unlockScript($scriptState);

        return $scriptState;
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return int
     */
    protected function calculateRemainProgressMax(ScriptState $scriptState)
    {
        $result = 0;
        foreach ($this->getSteps() as $index => $step) {
            if ($index < $scriptState->currentStep) {
                continue;
            }

            $result += $step->getProgressMax($scriptState);
        }

        return $result;
    }

    /**
     * @param ScriptState $state
     *
     * @return int|null
     */
    protected function getNextStepIndex(ScriptState $state): ?int
    {
        $index = $state->getNextStepIndex();

        do {
            try {
                $step = $this->getStepInstance($index++);

            } catch (ScriptExecutionError $e) {
                return null;
            }
        } while ($step->getProgressMax($state) === 0);

        return --$index; // return back one step because of index was already incremented
    }

    /**
     * @param int $index
     *
     * @return StepInterface
     * @throws ScriptExecutionError
     */
    protected function getStepInstance($index): StepInterface
    {
        $steps = $this->getSteps();
        $step  = $steps[$index] ?? null;

        if (!$step) {
            throw ScriptExecutionError::fromUnknownStep($index);
        }

        return $step;
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return bool
     */
    private function isNextStepAvailable(ScriptState $scriptState): bool
    {
        try {
            return $this->getStepInstance($this->getNextStepIndex($scriptState)) !== null;

        } catch (ScriptExecutionError $e) {
            // this exception must not be reported, because we check availability only
            return false;
        }

        return false;
    }

    /**
     * Checks if script is in the unlockable state
     *
     * @param ScriptState $state
     *
     * @return bool
     */
    private function canUnlock(ScriptState $state): bool
    {
        return in_array($state->state, [
            ScriptState::STATE_CANCELED,
            ScriptState::STATE_FINISHED_SUCCESSFULLY,
        ], true);
    }
}
