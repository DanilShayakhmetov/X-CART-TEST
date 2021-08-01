<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Execute;

use Psr\Log\LoggerInterface;
use Silex\Application;
use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Core\Archive\ArchiveFactory;
use XCart\Bus\Exception\Rebuild\AbortException;
use XCart\Bus\Exception\RebuildException;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;
use XCart\Bus\System\FilesystemInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "redeploy", weight = "2000")
 * @RebuildStep(script = "self-upgrade", weight = "2000")
 */
class UnpackPacks implements StepInterface
{
    /**
     * @var string
     */
    private $packsDir;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var ArchiveFactory
     */
    private $archiveFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $removePackage = false;

    /**
     * @param Application         $app
     * @param FilesystemInterface $filesystem
     * @param ArchiveFactory      $archiveFactory
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
        ArchiveFactory $archiveFactory,
        LoggerInterface $logger
    ) {
        return new self(
            $app['config']['module_packs_dir'],
            $filesystem,
            $archiveFactory,
            $logger
        );
    }

    /**
     * @param string              $packsDir
     * @param FilesystemInterface $filesystem
     * @param ArchiveFactory      $archiveFactory
     * @param LoggerInterface     $logger
     */
    public function __construct(
        $packsDir,
        FilesystemInterface $filesystem,
        ArchiveFactory $archiveFactory,
        LoggerInterface $logger
    ) {
        $this->packsDir       = $packsDir;
        $this->filesystem     = $filesystem;
        $this->archiveFactory = $archiveFactory;
        $this->logger         = $logger;
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return int
     */
    public function getProgressMax(ScriptState $scriptState): int
    {
        return $scriptState->isStepCompleted(DownloadPacks::class)
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
        $parentStepState = $scriptState->getCompletedStepState(DownloadPacks::class);
        if ($parentStepState) {
            return array_map(static function ($transition) {
                return [
                    'id'             => $transition['id'],
                    'transition'     => $transition['transition'],
                    'version_before' => $transition['version_before'],
                    'version_after'  => $transition['version_after'],
                    'pack_path'      => $transition['pack_path'],
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
                   'message' => 'rebuild.unpack_packs.state',
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
                   'message' => 'rebuild.unpack_packs.state.finished',
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
        $transition          = current($remainTransitions);
        $id                  = $transition['id'];

        $progressValue = $state->progressValue;

        // main action
        $transition = $this->unpackByTransition($transition);

        // update state
        $finishedTransitions[$id] = $transition;
        unset($remainTransitions[$id]);
        $progressValue++;

        // save state
        $state->remainTransitions   = $remainTransitions;
        $state->finishedTransitions = $finishedTransitions;
        $state->progressValue       = $progressValue;

        return $state;
    }

    /**
     * @param StepState $state
     *
     * @return StepState
     */
    private function skipStep(StepState $state): StepState
    {
        $state->finishedTransitions = array_map(static function ($item) {
            return $item + ['pack_dir' => ''];
        }, $state->remainTransitions);

        $state->remainTransitions = [];
        $state->progressValue     = $state->progressMax;

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
    private function unpackByTransition($transition): array
    {
        $path = $transition['pack_path'];
        $id   = $transition['id'];

        $this->logger->debug(sprintf('Extract package: %s', $path));

        if (!$this->filesystem->exists($path)) {
            $this->logger->critical(sprintf('File (%s) not found', $path));

            throw AbortException::fromUnpackStepMissingPackage($id, $path);
        }

        $packDir                = $this->packsDir . "{$id}.{$transition['version_after']}/";
        $transition['pack_dir'] = $packDir;

        if ($this->filesystem->exists($packDir)) {
            $this->filesystem->remove($packDir);
        }
        $this->filesystem->mkdir($packDir);

        if (!$this->archiveFactory->getUnpacker()->unpack($path, $packDir)) {
            $this->logger->critical(sprintf('Cannot extract (%s)', $path));

            throw AbortException::fromUnpackStepExtractionError($id, $path);
        }

        $this->logger->debug(sprintf('Package extracted: %s', $packDir));

        if ($this->removePackage) {
            $this->filesystem->remove($path);
        }

        return $transition;
    }
}
