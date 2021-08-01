<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Execute;

use Doctrine\Common\Cache\CacheProvider;
use Psr\Log\LoggerInterface;
use Silex\Application;
use Symfony\Component\Filesystem\Exception\IOException;
use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Domain\Backup\BackupInterface;
use XCart\Bus\Exception\Rebuild\AbortException;
use XCart\Bus\Exception\Rebuild\HoldException;
use XCart\Bus\Exception\RebuildException;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;
use XCart\Bus\System\FilesystemInterface;
use XCart\Bus\System\ResourceChecker;
use XCart\SilexAnnotations\Annotations\Service;
use XCart\SilexAnnotations\AnnotationServiceProvider;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "redeploy", weight = "8000")
 * @RebuildStep(script = "self-upgrade", weight = "4000")
 */
class MovePacks implements StepInterface
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
     * @var BackupInterface
     */
    private $backup;

    /**
     * @var ResourceChecker
     */
    private $resourceChecker;

    /**
     * @var CacheProvider
     */
    private $cacheProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $removeSource = false;

    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param Application         $app
     * @param FilesystemInterface $filesystem
     * @param BackupInterface     $backup
     * @param ResourceChecker     $resourceChecker
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
        BackupInterface $backup,
        ResourceChecker $resourceChecker,
        LoggerInterface $logger
    ) {
        return new self(
            $app['config']['root_dir'],
            $filesystem,
            $backup,
            $resourceChecker,
            $app[AnnotationServiceProvider::CACHE_SERVICE_NAME],
            $logger
        );
    }

    /**
     * @param string              $rootDir
     * @param FilesystemInterface $filesystem
     * @param BackupInterface     $backup
     * @param ResourceChecker     $resourceChecker
     * @param CacheProvider       $cacheProvider
     * @param LoggerInterface     $logger
     */
    public function __construct(
        $rootDir,
        FilesystemInterface $filesystem,
        BackupInterface $backup,
        ResourceChecker $resourceChecker,
        CacheProvider $cacheProvider,
        LoggerInterface $logger
    ) {
        $this->rootDir         = $rootDir;
        $this->filesystem      = $filesystem;
        $this->backup          = $backup;
        $this->resourceChecker = $resourceChecker;
        $this->cacheProvider   = $cacheProvider;
        $this->logger          = $logger;
        $this->cacheProvider   = $cacheProvider;
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return int
     */
    public function getProgressMax(ScriptState $scriptState): int
    {
        return $scriptState->isStepCompleted(UnpackPacks::class)
            ? count($this->getTransitions($scriptState))
            : count($this->filterScriptTransitions($scriptState->transitions));
    }

    /**
     * @param ScriptState $scriptState
     * @param StepState   $stepState
     *
     * @return StepState
     */
    public function initialize(ScriptState $scriptState, StepState $stepState = null): StepState
    {
        $parentStepState = $scriptState->getCompletedStepState(CheckPacks::class);
        $data            = $parentStepState->data;

        $preserved   = $data['preserved'] ?: [];
        $transitions = array_map(static function ($transition) use ($preserved) {
            $transition['new_files']    = array_values(array_diff($transition['new_files'], $preserved));
            $transition['remove_files'] = array_values(array_diff($transition['remove_files'], $preserved));

            return $transition;
        }, $this->getTransitions($scriptState));

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
        $this->backup = $this->backup->load($state->rebuildId);

        if ($action === self::ACTION_EXECUTE || $action === self::ACTION_RETRY) {
            $state = $this->processTransition($state);

        } elseif ($action === self::ACTION_IGNORE) {
            $state = $this->ignoreTransition($state);

        } elseif ($action === self::ACTION_SKIP_STEP) {
            $state = $this->skipStep($state);
        }

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
        $parentStepState = $scriptState->getCompletedStepState(CheckPacks::class);
        if ($parentStepState) {
            return array_map(static function ($transition) {
                return [
                    'id'           => $transition['id'],
                    'transition'   => $transition['transition'],
                    'new_files'    => $transition['new_files'],
                    'remove_files' => array_values(array_diff($transition['original_files'], $transition['new_files'])),
                    'pack_dir'     => $transition['pack_dir'],
                ];
            }, $parentStepState->finishedTransitions ?: []);
        }

        return [];
    }

    /**
     * @param array[] $transitions
     *
     * @return array[]
     */
    private function filterScriptTransitions($transitions): array
    {
        return array_filter($transitions, static function ($transition) {
            return in_array($transition['transition'], [
                ChangeUnitProcessor::TRANSITION_UPGRADE,
                ChangeUnitProcessor::TRANSITION_INSTALL_ENABLED,
                ChangeUnitProcessor::TRANSITION_INSTALL_DISABLED,
            ], true);
        });
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
                   'message' => 'rebuild.move_packs.state',
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
                   'message' => 'rebuild.move_packs.state.finished',
                   'params'  => [$finished, $total],
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
        $transition          = current($state->remainTransitions);
        $id                  = $transition['id'];

        $progressValue = $state->progressValue;

        // main action
        $transition = $this->moveByTransition($transition);

        // update state
        if (empty($transition['new_files']) && empty($transition['remove_files'])) {
            $finishedTransitions[$id] = $transition;
            unset($remainTransitions[$id]);
            $progressValue++;
        } else {
            $remainTransitions[$id] = $transition;
        }

        // save state
        $state->remainTransitions   = $remainTransitions;
        $state->finishedTransitions = $finishedTransitions;
        $state->progressValue       = $progressValue;

        if ($id === 'XC-Service') {
            $this->cacheProvider->flushAll();

            $this->logger->debug(
                'Request page reloading',
                [
                    'id' => $state->rebuildId,
                ]
            );

            throw HoldException::fromReloadPageStepReload($state);
        }

        return $state;
    }

    /**
     * @param StepState $state
     *
     * @return StepState
     */
    private function skipStep(StepState $state): StepState
    {
        $state->finishedTransitions = $state->remainTransitions;
        $state->remainTransitions   = [];
        $state->progressValue       = $state->progressMax;

        return $state;
    }

    /**
     * @param StepState $state
     *
     * @return StepState
     */
    private function ignoreTransition(StepState $state): StepState
    {
        $remainTransitions = $state->remainTransitions;
        if (empty($remainTransitions)) {
            return $state;
        }

        $transition = current($remainTransitions);
        $id         = $transition['id'];

        unset($remainTransitions[$id]);

        $state->remainTransitions = $remainTransitions;
        $state->progressValue++;

        return $state;
    }

    /**
     * @param array $transition
     *
     * @return array
     * @throws RebuildException
     */
    private function moveByTransition($transition): array
    {
        $packDir = $transition['pack_dir'];

        $files = isset($transition['files'])
            ? $transition['files']
            : [
                'updated' => [],
                'created' => [],
                'removed' => [],
            ];

        try {
            if ($transition['new_files']) {
                foreach ($transition['new_files'] as $k => $file) {
                    if ($this->filesystem->exists($this->rootDir . $file)) {
                        $this->logger->debug(sprintf('Update: %s', $file));

                        $this->backup->addReplaceRecord($file);

                        $files['updated'][] = $file;

                    } else {
                        $this->logger->debug(sprintf('Create: %s', $file));

                        $this->backup->addCreateRecord($file);

                        $files['created'][] = $file;
                    }

                    $this->filesystem->copy(
                        $packDir . $file,
                        $this->rootDir . $file,
                        true
                    );

                    unset($transition['new_files'][$k]);

                    if ($this->resourceChecker->timeRemain() < 5000) {
                        break;
                    }
                }
            }

            if ($transition['remove_files'] && $this->resourceChecker->timeRemain() > 5000) {
                foreach ($transition['remove_files'] as $k => $file) {
                    if ($this->filesystem->exists($this->rootDir . $file)) {
                        $this->backup->addReplaceRecord($file);

                        $this->filesystem->remove($this->rootDir . $file);

                        $files['removed'] = $file;

                        $this->logger->debug(sprintf('Remove: %s', $file));
                    }

                    unset($transition['remove_files'][$k]);

                    if ($this->resourceChecker->timeRemain() < 5000) {
                        break;
                    }
                }
            }
        } catch (IOException $e) {
            $this->logger->critical(sprintf('Updating files error: %s', $e->getMessage()));

            throw new AbortException($e->getMessage(), $e->getCode());
        }

        $transition['files'] = $files;

        if ($this->removeSource) {
            $this->filesystem->remove($packDir);
        }

        return $transition;
    }
}
