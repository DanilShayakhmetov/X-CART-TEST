<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Rollback;

use Psr\Log\LoggerInterface;
use Silex\Application;
use SplFileInfo;
use Symfony\Component\Filesystem\Exception\IOException;
use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Domain\Backup\BackupInterface;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\Execute\MovePacks;
use XCart\Bus\Rebuild\Executor\Step\Execute\RemoveModules;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\Bus\System\FilesystemInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "rollback", weight = "3000")
 * @RebuildStep(script = "self-rollback", weight = "1000")
 */
class RestoreFiles implements StepInterface
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Application         $app
     * @param FilesystemInterface $filesystem
     * @param BackupInterface     $backup
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
        LoggerInterface $logger
    ) {
        return new self(
            $app['config']['root_dir'],
            $filesystem,
            $backup,
            $logger
        );
    }

    /**
     * @param string              $rootDir
     * @param FilesystemInterface $filesystem
     * @param BackupInterface     $backup
     * @param LoggerInterface     $logger
     */
    public function __construct(
        $rootDir,
        FilesystemInterface $filesystem,
        BackupInterface $backup,
        LoggerInterface $logger
    ) {
        $this->rootDir    = $rootDir;
        $this->filesystem = $filesystem;
        $this->backup     = $backup;
        $this->logger     = $logger;
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return int
     */
    public function getProgressMax(ScriptState $scriptState): int
    {
        $parentScriptState = $scriptState->parentState;

        $removeModulesStepState = $parentScriptState->stepState->id === RemoveModules::class
            ? $parentScriptState->stepState
            : $parentScriptState->getCompletedStepState(RemoveModules::class);

        $movePacksStepState = $parentScriptState->stepState->id === MovePacks::class
            ? $parentScriptState->stepState
            : $parentScriptState->getCompletedStepState(MovePacks::class);

        return (int) ($removeModulesStepState || $movePacksStepState);
    }

    /**
     * Generate fresh StepState for correct execution
     *
     * @param ScriptState $scriptState
     * @param StepState   $stepState
     *
     * @return StepState
     */
    public function initialize(ScriptState $scriptState, StepState $stepState = null): StepState
    {
        $this->logger->info(get_class($this) . ':' . __FUNCTION__);

        $state = new StepState([
            'id'            => static::class,
            'state'         => StepState::STATE_INITIALIZED,
            'rebuildId'     => $scriptState->id,
            'progressMax'   => $this->getProgressMax($scriptState),
            'progressValue' => 0,
        ]);

        $state->currentActionInfo = $this->getCurrentActionInfoMessage($state);

        return $state;
    }

    /**
     * @param StepState $state Cloned state
     * @param string    $action
     * @param array     $params
     *
     * @return StepState
     */
    public function execute(StepState $state, $action = self::ACTION_EXECUTE, array $params = []): StepState
    {
        $backup  = $this->backup->load($state->rebuildId);
        $created = $backup->getCreated() ?: [];

        $this->removeFiles($created);
        $this->restoreFiles($backup->getContentList());

        $state->progressValue++;

        $state->state = StepState::STATE_FINISHED_SUCCESSFULLY;

        $state->currentActionInfo  = $this->getCurrentActionInfoMessage($state);
        $state->finishedActionInfo = $this->getFinishedActionInfoMessage($state);

        return $state;
    }

    /**
     * @param StepState $state
     *
     * @return string[]
     */
    private function getCurrentActionInfoMessage(StepState $state): array
    {
        return [[
                    'message' => 'rollback.restore_files.state',
                    'params'  => [],
                ]];
    }

    /**
     * @param StepState $state
     *
     * @return string[]
     */
    private function getFinishedActionInfoMessage(StepState $state): array
    {
        return [[
                    'message' => 'rollback.restore_files.state.finished',
                    'params'  => [],
                ]];
    }

    /**
     * @param array $files
     *
     * @return bool
     */
    private function removeFiles(array $files): ?bool
    {
        try {
            foreach ($files as $file) {
                $this->logger->debug(sprintf('Remove: %s', $file));

                if ($file) {
                    $this->filesystem->remove($this->rootDir . $file);
                }
            }

            return true;

        } catch (IOException $e) {
            $this->logger->critical(
                'Removing files error',
                [
                    'message' => $e->getMessage(),
                    'files'   => $files,
                ]
            );

            return false;
        }
    }

    /**
     * Return updated files, new files and
     *
     * @param array $files
     *
     * @return bool
     */
    private function restoreFiles($files): ?bool
    {
        try {
            /** @var SplFileInfo $file */
            foreach ($files as $file => $path) {
                $this->logger->debug(sprintf('Restore: %s', $file));

                $this->filesystem->copy(
                    $path,
                    $this->rootDir . $file,
                    true
                );
            }

            return true;

        } catch (IOException $e) {
            $this->logger->critical(
                'Restoring files error',
                [
                    'message' => $e->getMessage(),
                    'files'   => $files,
                ]
            );

            return false;
        }
    }
}
