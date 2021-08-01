<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step;

use Psr\Log\LoggerInterface;
use Silex\Application;
use Symfony\Component\Filesystem\Exception\IOException;
use XCart\Bus\Exception\RebuildException;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\Bus\System\FilesystemInterface;
use XCart\SilexAnnotations\Annotations\Service;

abstract class ARemoveOldCache implements StepInterface
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $removeStartTime;

    private $removeTimeLimit = 15;

    private $tickCount = 0;

    /**
     * @param Application         $app
     * @param FilesystemInterface $filesystem
     * @param LoggerInterface     $logger
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        FilesystemInterface $filesystem,
        LoggerInterface $logger
    ) {
        return new static(
            $app['config']['root_dir'],
            $filesystem,
            $logger
        );
    }

    /**
     * @param string              $rootDir
     * @param FilesystemInterface $filesystem
     * @param LoggerInterface     $logger
     */
    public function __construct(
        $rootDir,
        FilesystemInterface $filesystem,
        LoggerInterface $logger
    ) {
        $this->rootDir    = $rootDir;
        $this->filesystem = $filesystem;
        $this->logger     = $logger;
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
        $transitions = $this->getTransitions($scriptState);

        $this->logger->info(get_class($this) . ':' . __FUNCTION__);
        $this->logger->debug(
            get_class($this) . ':' . __FUNCTION__,
            [
                'transitions' => $transitions,
            ]
        );

        $state = new StepState([
            'id'                  => static::class,
            'state'               => StepState::STATE_INITIALIZED,
            'rebuildId'           => $scriptState->id,
            'remainTransitions'   => $transitions,
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
     * @throws RebuildException
     */
    public function execute(StepState $state, $action = self::ACTION_EXECUTE, array $params = []): StepState
    {
        $state = $this->processTransition($state);

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
     * @return array
     */
    private function getTransitions(ScriptState $scriptState): array
    {
        $masks = [
            $this->rootDir . 'var/run.old.*',
            $this->rootDir . 'var/locale.old.*',
            $this->rootDir . 'var/datacache.old.*',
            $this->rootDir . 'var/tmp.old.*',
            $this->rootDir . 'var/resources.old.*',
        ];

        $transitions = [];

        foreach ($masks as $mask) {
            $transitions[] = [
                'mask' => $mask,
            ];
        }

        return $transitions;
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
                   'message' => 'rebuild.action.remove_old_cache.state',
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
                   'message' => 'rebuild.action.remove_old_cache.state.finished',
               ]]
            : [];
    }

    /**
     * @param StepState $state
     *
     * @return StepState
     * @throws RebuildException
     */
    private function processTransition(StepState $state): StepState
    {
        $remainTransitions = $state->remainTransitions;
        if (empty($remainTransitions)) {
            return $state;
        }

        $finishedTransitions = $state->finishedTransitions;
        $transition          = current($remainTransitions);
        $id                  = key($remainTransitions);

        $progressValue = $state->progressValue;

        // main action
        try {
            $transition = $this->removeDirByTransition($transition);
        } catch (\Throwable $e) {
            $transition['finished'] = true;
            $this->logger->error(sprintf('Exception while removing old cache: %s', $e->getMessage()));
        }

        // update state
        if (!empty($transition['finished'])) {
            // move transition from remain to finished
            $finishedTransitions[$id] = $transition;
            unset($remainTransitions[$id]);
            $progressValue++;

        } else {
            // save updated transition to remain
            $remainTransitions[$id] = $transition;
        }

        // save state
        $state->remainTransitions   = $remainTransitions;
        $state->finishedTransitions = $finishedTransitions;
        $state->progressValue       = $progressValue;

        return $state;
    }

    /**
     * @param array $transition
     *
     * @return array
     */
    private function removeDirByTransition($transition): array
    {
        if ($dirs = glob($transition['mask'], GLOB_ONLYDIR)) {
            $this->initializeRemoveTimer();

            foreach ($dirs as $dir) {
                $iterator = new \RecursiveDirectoryIterator($dir, \FilesystemIterator::CURRENT_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS);

                while ($this->hasTime() && $iterator->valid()) {
                    $filePath = $iterator->current();
                    if (!$iterator->hasChildren()) {
                        $iterator->next();
                        continue;
                    }
                    $iterator->next();

                    $this->registerTick();

                    try {
                        $this->filesystem->remove($filePath);
                    } catch (IOException $e) {
                        $this->logger->error(sprintf('IOException while removing %s: %s', $filePath, $e->getMessage()));
                        throw $e;
                    }
                }

                if (!$iterator->valid()) {
                    try {
                        $this->filesystem->remove($dir);
                    } catch (IOException $e) {
                        $this->logger->error(sprintf('IOException while removing %s: %s', $dir, $e->getMessage()));
                        throw $e;
                    }
                }
            }

            if (empty(glob($transition['mask'], GLOB_ONLYDIR))) {
                $transition['finished'] = true;
            }

        } else {
            $transition['finished'] = true;
        }

        return $transition;
    }

    private function initializeRemoveTimer(): void
    {
        $this->removeStartTime = microtime(true);
    }

    /**
     * @return bool
     */
    private function hasTime(): bool
    {
        $passed = microtime(true) - $this->removeStartTime;

        $averageTick = $this->tickCount ? $passed / $this->tickCount : 0;

        return $passed + $averageTick < $this->removeTimeLimit;
    }

    private function registerTick(): void
    {
        $this->tickCount++;
    }
}

