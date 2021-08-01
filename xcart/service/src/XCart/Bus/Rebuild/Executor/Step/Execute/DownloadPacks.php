<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Execute;

use Psr\Log\LoggerInterface;
use Silex\Application;
use XCart\Bus\Client\MarketplaceClient;
use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Core\Archive\ArchiveFactory;
use XCart\Bus\Exception\MarketplaceException;
use XCart\Bus\Exception\Rebuild\AbortException;
use XCart\Bus\Exception\RebuildException;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;
use XCart\Bus\System\FilesystemInterface;
use XCart\Marketplace\RangeIterator;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "redeploy", weight = "1000")
 * @RebuildStep(script = "self-upgrade", weight = "1000")
 */
class DownloadPacks implements StepInterface
{
    /**
     * @var string
     */
    private $packsDir;

    /**
     * @var MarketplaceClient
     */
    private $marketplaceClient;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ArchiveFactory
     */
    private $archiveFactory;

    private $downloadStartTime;

    private $downloadTimeLimit = 15;

    private $tickCount = 0;

    /**
     * @param Application         $app
     * @param MarketplaceClient   $marketplaceClient
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
        MarketplaceClient $marketplaceClient,
        FilesystemInterface $filesystem,
        ArchiveFactory $archiveFactory,
        LoggerInterface $logger
    ) {
        return new self(
            $app['config']['module_packs_dir'],
            $marketplaceClient,
            $filesystem,
            $archiveFactory,
            $logger
        );
    }

    /**
     * @param string              $packsDir
     * @param MarketplaceClient   $marketplaceClient
     * @param FilesystemInterface $filesystem
     * @param ArchiveFactory      $archiveFactory
     * @param LoggerInterface     $logger
     */
    public function __construct(
        $packsDir,
        MarketplaceClient $marketplaceClient,
        FilesystemInterface $filesystem,
        ArchiveFactory $archiveFactory,
        LoggerInterface $logger
    ) {
        $this->packsDir          = $packsDir;
        $this->marketplaceClient = $marketplaceClient;
        $this->filesystem        = $filesystem;
        $this->logger            = $logger;
        $this->archiveFactory    = $archiveFactory;
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
        $this->filesystem->mkdir($this->packsDir);

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
            'currentActionInfo'   => [],
            'finishedActionInfo'  => [],
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
     *
     * @throws RebuildException
     */
    public function execute(StepState $state, $action = self::ACTION_EXECUTE, array $params = []): StepState
    {
        if ($action === self::ACTION_EXECUTE || $action === self::ACTION_RETRY) {
            $state = $this->processTransition($state);

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
     * Filter transitions by type
     *
     * @param ScriptState $scriptState
     *
     * @return array[]
     */
    private function getTransitions(ScriptState $scriptState): array
    {
        $transitions = array_filter($scriptState->transitions ?? [], static function ($transition) {
            return in_array($transition['transition'], [
                ChangeUnitProcessor::TRANSITION_UPGRADE,
                ChangeUnitProcessor::TRANSITION_INSTALL_ENABLED,
                ChangeUnitProcessor::TRANSITION_INSTALL_DISABLED,
            ], true);
        });

        return array_map(static function ($transition) {
            return [
                'id'             => $transition['id'],
                'transition'     => $transition['transition'],
                'version_before' => $transition['stateBeforeTransition']['version'] ?? '',
                'version_after'  => $transition['stateAfterTransition']['version'],
            ];
        }, $transitions);
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
                   'message' => 'rebuild.download_packs.state',
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
                   'message' => 'rebuild.download_packs.state.finished',
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
        $transition = $this->downloadByTransition($transition);

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
     * @param StepState $state
     *
     * @return StepState
     */
    private function skipStep(StepState $state): StepState
    {
        $state->finishedTransitions = array_map(static function ($item) {
            return $item + ['pack_path' => ''];
        }, $state->remainTransitions);

        $state->remainTransitions = [];
        $state->progressValue     = $state->progressMax;

        return $state;
    }

    /**
     * @param array $transition
     *
     * @return array
     * @throws RebuildException
     */
    private function downloadByTransition($transition): array
    {
        $packPathWithoutExtension = "{$this->packsDir}{$transition['id']}.{$transition['version_after']}";

        // pack already downloaded
        foreach ($this->archiveFactory->getUnpacker()->getAvailableExtensions() as $ext) {
            $packPath = "{$packPathWithoutExtension}{$ext}";
            if ($this->filesystem->exists($packPath)) {
                $transition['pack_path'] = $packPath;
                $transition['finished'] = true;

                $this->logger->notice(sprintf('Package already exists: %s', $transition['pack_path']));

                return $transition;
            }
        }

        $extension = $this->archiveFactory->getUnpacker()->canCompress()
            ? 'tgz'
            : 'tar';
        $transition['pack_path'] = "{$packPathWithoutExtension}.{$extension}";

        $partPath = $transition['pack_path'] . '.part';

        // new download action, remove temporary data
        if (empty($transition['state'])) {
            $this->filesystem->remove([$transition['pack_path'], $partPath]);
        }

        $this->initializeDownloadTimer();

        try {
            $iterator = $this->requestDataIterator(
                $transition['id'],
                $transition['version_after'],
                $transition['state'] ?? []
            );

            if ($iterator->valid()) {
                $transition['state'] = $iterator->getState() ?? [];
            }

            while ($this->hasTime() && $iterator->valid()) {
                $state = $transition['state']
                    ? $transition['state']['position'] . '/' . $transition['state']['total']
                    : '';

                $this->logger->info(sprintf('Package downloading in progress: %s (%s)', $partPath, $state));

                $data = $iterator->current();
                $this->registerTick();
                $this->filesystem->appendToFile($partPath, $data);
                $iterator->next();
                $transition['state'] = $iterator->getState() ?? [];
            }
        } catch (MarketplaceException $e) {
            $this->logger->critical(sprintf('Package download error: %s', $e->getMessage()));

            throw AbortException::fromDownloadStepWrongResponse($transition['id'], $e->getMessage());
        }

        if (!$iterator->valid()) {
            $content = @file_get_contents($partPath);

            if ($data = @json_decode($content, true)) {
                $this->filesystem->remove($partPath);

                $this->logger->critical(sprintf('Package download error: %s', $data['message']));

                throw AbortException::fromDownloadStepWrongResponse($transition['id'], $data['message']);
            }

            if (empty($content)) {
                $this->filesystem->remove($partPath);

                $this->logger->critical(sprintf('Package download error: %s', 'Empty response'));

                throw AbortException::fromDownloadStepEmptyResponse($transition['id']);
            }

            $this->filesystem->rename($partPath, $transition['pack_path']);
            $transition['finished'] = true;

            $state = $transition['state']
                ? $transition['state']['position'] . '/' . $transition['state']['total']
                : '';

            $this->logger->info(sprintf('Package downloaded successfully: %s (%s)', $transition['pack_path'], $state));
        }

        return $transition;
    }

    private function initializeDownloadTimer(): void
    {
        $this->downloadStartTime = microtime(true);
    }

    /**
     * @return bool
     */
    private function hasTime(): bool
    {
        $passed = microtime(true) - $this->downloadStartTime;

        $averageTick = $this->tickCount ? $passed / $this->tickCount : 0;

        return $passed + $averageTick < $this->downloadTimeLimit;
    }

    private function registerTick(): void
    {
        $this->tickCount++;
    }

    /**
     * @param string $id
     * @param string $version
     * @param array  $state
     *
     * @return RangeIterator
     * @throws MarketplaceException
     */
    private function requestDataIterator($id, $version, array $state): RangeIterator
    {
        /** @throws MarketplaceException */
        return $this->marketplaceClient->getPackIterator(
            $id,
            $version,
            $state,
            $this->archiveFactory->getUnpacker()->canCompress()
        );
    }
}
