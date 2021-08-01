<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Execute;

use AppendIterator;
use FilesystemIterator;
use Psr\Log\LoggerInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Silex\Application;
use Symfony\Component\Filesystem\Exception\IOException;
use XCart\Bus\Client\MarketplaceClient;
use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Domain\ModuleInfoProvider;
use XCart\Bus\Exception\Rebuild\AbortException;
use XCart\Bus\Exception\Rebuild\HoldException;
use XCart\Bus\Exception\RebuildException;
use XCart\Bus\Query\Data\KnownHashesCacheDataSource;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;
use XCart\Bus\System\FilesystemInterface;
use XCart\Bus\System\ResourceChecker;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "redeploy", weight = "2500")
 * @RebuildStep(script = "self-upgrade", weight = "2500")
 */
class CheckFS implements StepInterface
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var KnownHashesCacheDataSource
     */
    private $knownHashesCacheDataSource;

    /**
     * @var MarketplaceClient
     */
    private $marketplaceClient;

    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var ResourceChecker
     */
    private $resourceChecker;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Application                $app
     * @param KnownHashesCacheDataSource $knownHashesCacheDataSource
     * @param MarketplaceClient          $marketplaceClient
     * @param ModuleInfoProvider         $moduleInfoProvider
     * @param FilesystemInterface        $filesystem
     * @param ResourceChecker            $resourceChecker
     * @param LoggerInterface            $logger
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        KnownHashesCacheDataSource $knownHashesCacheDataSource,
        MarketplaceClient $marketplaceClient,
        ModuleInfoProvider $moduleInfoProvider,
        FilesystemInterface $filesystem,
        ResourceChecker $resourceChecker,
        LoggerInterface $logger
    ) {
        return new self(
            $app['config']['root_dir'],
            $knownHashesCacheDataSource,
            $marketplaceClient,
            $moduleInfoProvider,
            $filesystem,
            $resourceChecker,
            $logger
        );
    }

    /**
     * @param string                     $rootDir
     * @param KnownHashesCacheDataSource $knownHashesCacheDataSource
     * @param MarketplaceClient          $marketplaceClient
     * @param ModuleInfoProvider         $moduleInfoProvider
     * @param FilesystemInterface        $filesystem
     * @param ResourceChecker            $resourceChecker
     * @param LoggerInterface            $logger
     */
    public function __construct(
        $rootDir,
        KnownHashesCacheDataSource $knownHashesCacheDataSource,
        MarketplaceClient $marketplaceClient,
        ModuleInfoProvider $moduleInfoProvider,
        FilesystemInterface $filesystem,
        ResourceChecker $resourceChecker,
        LoggerInterface $logger
    ) {
        $this->rootDir                    = $rootDir;
        $this->knownHashesCacheDataSource = $knownHashesCacheDataSource;
        $this->marketplaceClient          = $marketplaceClient;
        $this->moduleInfoProvider         = $moduleInfoProvider;
        $this->filesystem                 = $filesystem;
        $this->resourceChecker            = $resourceChecker;
        $this->logger                     = $logger;
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
            'data'                => [
                'errors' => [],
            ],
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
        switch ($action) {
            /** @noinspection PhpMissingBreakStatementInspection */
            case self::ACTION_RETRY:
                $state->remainTransitions   = array_map(static function ($transition) {
                    $transition['check']   = $transition['checked'];
                    $transition['checked'] = [];

                    return $transition;
                }, $state->finishedTransitions);
                $state->finishedTransitions = [];
                $state->data                = [
                    'errors' => [],
                ];
                $state->progressValue       = 0;
            case self::ACTION_EXECUTE:
                $state = $this->processTransition($state);

                if (empty($state->remainTransitions) && !empty($state->data['errors'])) {
                    $commands = $this->getCommands($state->data['errors']);

                    throw HoldException::fromCheckFSErrorsPresent($state, $commands);
                }

                break;

            case self::ACTION_SKIP_STEP:
                $state = $this->skipStep($state);

                break;
            default:
                break;
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
        $transitions = [];

        $parentStepState = $scriptState->getCompletedStepState(UnpackPacks::class);
        if ($parentStepState) {
            $transitions = array_map(static function ($transition) {
                return [
                    'id'             => $transition['id'],
                    'transition'     => $transition['transition'],
                    'version_before' => $transition['version_before'],
                    'version_after'  => $transition['version_after'],
                    'pack_dir'       => $transition['pack_dir'],
                    'hashes'         => [
                        'original' => null,
                        'new'      => null,
                    ],
                    'check'          => null,
                    'checked'        => [],
                    'errors'         => [],
                ];
            }, $parentStepState->finishedTransitions ?: []);
        }

        foreach ($scriptState->transitions as $transition) {
            if ($transition['transition'] === ChangeUnitProcessor::TRANSITION_REMOVE) {
                $transitions[$transition['id']] = [
                    'id'             => $transition['id'],
                    'transition'     => $transition['transition'],
                    'version_before' => $transition['stateBeforeTransition']['version'],
                    'version_after'  => $transition['stateAfterTransition']['version'],
                    'pack_dir'       => null,
                    'hashes'         => [
                        'original' => null,
                        'new'      => null,
                    ],
                    'check'          => null,
                    'checked'        => [],
                    'errors'         => [],
                ];
            }
        }

        return $transitions;
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
                ChangeUnitProcessor::TRANSITION_REMOVE,
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
                   'message' => 'rebuild.check_fs.state',
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
                   'message' => 'rebuild.check_fs.state.finished',
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

        $data = $state->data;

        // main action
        $transition     = $this->checkTransition($transition);
        $data['errors'] = array_unique(array_merge($data['errors'] ?? [], $transition['errors']));

        // update state
        if (empty($transition['check'])) {
            $finishedTransitions[$id] = $transition;
            unset($remainTransitions[$id]);
            $progressValue++;
        } else {
            $remainTransitions[$id] = $transition;
        }

        // save state
        $state->data                = $data;
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
    private function checkTransition($transition): array
    {
        $errors = [];

        if (!isset($transition['hashes']['original'])) {
            if ($transition['transition'] === ChangeUnitProcessor::TRANSITION_REMOVE) {
                $transition['hashes']['original'] = $this->getOriginalHashesFromFiles($transition['id']);

            } else {
                $transition['hashes']['original'] = $transition['version_before']
                    ? $this->getOriginalHashes($transition['id'], $transition['version_before'])
                    : [];
            }
        }

        if (!isset($transition['hashes']['new'])) {
            if ($transition['pack_dir']) {
                $transition['hashes']['new'] = $this->getNewHashes($transition['pack_dir']);

                $this->knownHashesCacheDataSource->saveOne(
                    $transition['hashes']['new'],
                    md5($transition['id'] . '|' . $transition['version_after'])
                );
            } else {
                $transition['hashes']['new'] = [];
            }
        }

        if (!isset($transition['check'])) {
            $originalFiles       = array_keys($transition['hashes']['original']);
            $newFiles            = array_keys($transition['hashes']['new']);
            $transition['check'] = array_unique(array_merge($newFiles, array_values(array_diff($originalFiles, $newFiles))));
        }

        try {
            if ($transition['check']) {
                foreach ($transition['check'] as $k => $file) {
                    $filePath = $this->rootDir . $file;
                    if ($this->filesystem->exists($filePath)) {
                        $this->logger->debug(sprintf('Check file: %s', $filePath));

                        if (!is_writable($filePath)) {
                            $errors[] = $filePath;
                        }

                        $dirPath = dirname($filePath);
                        if (!is_writable($dirPath)) {
                            $errors[] = $dirPath;
                        }

                    } else {
                        $nearestDirectory = $this->filesystem->getNearestExistingDirectory($filePath, $this->rootDir);
                        $this->logger->debug(sprintf('Check file: %s (%s)', $filePath, $nearestDirectory));
                        if (!is_writable($nearestDirectory)) {
                            $errors[] = $nearestDirectory;
                        }
                    }

                    $transition['checked'][] = $file;
                    unset($transition['check'][$k]);

                    if ($this->resourceChecker->timeRemain() < 5000) {
                        break;
                    }
                }
            }
        } catch (IOException $e) {
            $this->logger->critical(sprintf('Check FS integrity error: %s', $e->getMessage()));

            throw new AbortException($e->getMessage(), $e->getCode());
        }

        $transition['errors'] = $errors;

        return $transition;
    }

    /**
     * @param string $id
     * @param string $version
     *
     * @return array
     * @throws RebuildException
     */
    private function getOriginalHashes($id, $version): array
    {
        $hashes = $this->knownHashesCacheDataSource->find(md5($id . '|' . $version));
        if ($hashes) {
            return $hashes;
        }

        $response = $this->marketplaceClient->getHashes($id, $version);

        if (!is_array($response)) {
            $this->logger->critical(
                'Invalid response from markeptlace',
                [
                    'response' => $response,
                ]
            );

            throw AbortException::fromCheckFSStepInvalidResponse($id);
        }

        if (!empty($response['error'])) {
            $this->logger->critical(sprintf('Invalid response from markeptlace: %s', $response['message']));

            throw AbortException::fromCheckFSStepWrongResponse($id, $response['message']);
        }

        $this->knownHashesCacheDataSource->saveOne(
            $response,
            md5($id . '|' . $version)
        );

        return $response;
    }

    /**
     * @param string $moduleId
     *
     * @return array
     */
    private function getOriginalHashesFromFiles($moduleId): array
    {
        $files = new AppendIterator();

        $moduleInfo = $this->moduleInfoProvider->getModuleInfo($moduleId);

        if (isset($moduleInfo['directories'])) {
            foreach ((array) $moduleInfo['directories'] as $directory) {
                if (is_dir($directory)) {
                    $files->append(new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
                    ));
                }
            }
        }

        $result = [];

        foreach ($files as $filePath => $fileInfo) {
            $path = $this->filesystem->makePathRelative(dirname($filePath), $this->rootDir);

            $result[$path . basename($filePath)] = $filePath;
        }

        return array_map('md5_file', $result);
    }

    /**
     * @param string $packageDir
     *
     * @return array
     * @throws RebuildException
     */
    private function getNewHashes($packageDir): array
    {
        $newHashes = $this->readHashesFromFile($packageDir);
        if ($newHashes) {
            return $newHashes;
        }

        $result = [];
        if (is_dir($packageDir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($packageDir, FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $filePath => $fileInfo) {
                $path = $this->filesystem->makePathRelative(dirname($filePath), $packageDir);

                $result[($path === './' ? '' : $path) . basename($filePath)] = md5_file($filePath);
            }
        }

        return $result;
    }

    /**
     * @param string $packageDir
     *
     * @return array
     * @throws RebuildException
     */
    private function readHashesFromFile($packageDir): array
    {
        $path = $packageDir . '/.hash';

        if (!$this->filesystem->exists($path)) {
            return [];
        }

        if (!($data = @json_decode(file_get_contents($path), true))) {
            throw AbortException::fromCheckFSStepWrongHashFile($path);
        }

        return $data;
    }

    /**
     * @param array $errors
     *
     * @return array
     */
    private function getCommands($errors): array
    {
        $result = [];

        $entries = [
            'directories' => [],
            'files'      => [],
        ];

        foreach ($errors as $path) {
            $commonPath = $this->getCommonPath($path);
            if ($commonPath) {
                if (empty($result[$commonPath])) {
                    $result[$commonPath] = is_dir($path)
                        ? 'find ' . $commonPath . ' -type d -execdir chmod 777 "{}" \\;'
                        : 'find ' . $commonPath . ' -type f -execdir chmod 666 "{}" \\;';
                }
            } else {
                $entries[is_dir($path) ? 'directories' : 'files'][] = $path;
            }
        }

        foreach ($entries as $type => $paths) {
            if ($paths) {
                $permission = ($type == 'directories') ? '777' : '666';
                $result[]   = 'chmod ' . $permission . ' ' . implode(' ', array_unique($paths)) . ';';

            }
        }

        return array_values($result);
    }

    /**
     * @param string $path
     *
     * @return string|null
     */
    private function getCommonPath($path): ?string
    {
        $commonPaths = [
            'classes',
            'skins',
            'Includes',
            'sql',
        ];

        foreach ($commonPaths as $commonPath) {
            if (strpos($path, $this->rootDir . $commonPath) === 0) {
                return $this->rootDir . $commonPath;
            }
        }

        return null;
    }
}
