<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Execute;

use Exception;
use Psr\Log\LoggerInterface;
use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Domain\Module;
use XCart\Bus\Domain\ModuleInfoProvider;
use XCart\Bus\Exception\Rebuild\AbortException;
use XCart\Bus\Exception\RebuildException;
use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Query\Data\MarketplaceModulesDataSource;
use XCart\Bus\Query\Data\SetDataSource;
use XCart\Bus\Query\Data\UploadedModulesDataSource;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "redeploy", weight = "14000")
 * @RebuildStep(script = "self-upgrade", weight = "5000")
 * @RebuildStep(script = "install", weight = "2000")
 */
class UpdateDataSource implements StepInterface
{
    /**
     * @var InstalledModulesDataSource
     */
    private $installedModulesDataSource;

    /**
     * @var MarketplaceModulesDataSource
     */
    private $marketplaceModulesDataSource;

    /**
     * @var UploadedModulesDataSource
     */
    private $uploadedModulesDataSource;

    /**
     * @var CoreConfigDataSource
     */
    private $coreConfigDataSource;

    /**
     * @var SetDataSource
     */
    private $setDataSource;

    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param InstalledModulesDataSource   $installedModulesDataSource
     * @param MarketplaceModulesDataSource $marketplaceModulesDataSource
     * @param UploadedModulesDataSource    $uploadedModulesDataSource
     * @param CoreConfigDataSource         $coreConfigDataSource
     * @param SetDataSource                $setDataSource
     * @param ModuleInfoProvider           $moduleInfoProvider
     * @param LoggerInterface              $logger
     */
    public function __construct(
        InstalledModulesDataSource $installedModulesDataSource,
        MarketplaceModulesDataSource $marketplaceModulesDataSource,
        UploadedModulesDataSource $uploadedModulesDataSource,
        CoreConfigDataSource $coreConfigDataSource,
        SetDataSource $setDataSource,
        ModuleInfoProvider $moduleInfoProvider,
        LoggerInterface $logger
    ) {
        $this->installedModulesDataSource   = $installedModulesDataSource;
        $this->marketplaceModulesDataSource = $marketplaceModulesDataSource;
        $this->uploadedModulesDataSource    = $uploadedModulesDataSource;
        $this->coreConfigDataSource         = $coreConfigDataSource;
        $this->setDataSource                = $setDataSource;
        $this->moduleInfoProvider           = $moduleInfoProvider;
        $this->logger                       = $logger;
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return int
     */
    public function getProgressMax(ScriptState $scriptState): int
    {
        return 1;
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
     *
     * @throws RebuildException
     * @throws Exception
     */
    public function execute(StepState $state, $action = self::ACTION_EXECUTE, array $params = []): StepState
    {
        $this->processTransitions($state);
        $this->refreshInstalledModulesDataSource();

        $this->coreConfigDataSource->dataDate  = time();
        $this->coreConfigDataSource->cacheDate = 0;

        $this->marketplaceModulesDataSource->clear();
        $this->setDataSource->clear();

        $state->finishedTransitions = $state->remainTransitions;
        $state->remainTransitions   = [];
        $state->progressValue++;

        $state->state = StepState::STATE_FINISHED_SUCCESSFULLY;

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
        return $scriptState->transitions ?: [];
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

        return $state->state !== StepState::STATE_FINISHED_SUCCESSFULLY
            ? [[
                   'message' => 'rebuild.update_data_source.state',
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

        return $state->state === StepState::STATE_FINISHED_SUCCESSFULLY
            ? [[
                   'message' => 'rebuild.update_data_source.state.finished',
                   'params'  => [$finished, $total],
               ]]
            : [];
    }

    /**
     * @param StepState $state
     *
     * @throws RebuildException
     * @throws Exception
     */
    private function processTransitions(StepState $state): void
    {
        if ($state->remainTransitions) {
            foreach ($state->remainTransitions as $key => $transition) {
                $this->logger->debug(sprintf('Update data: %s', $transition['id']));

                /** @var Module $module */
                $module     = $this->installedModulesDataSource->find($transition['id']);
                $stateAfter = $transition['stateAfterTransition'];

                if (!empty($stateAfter['installedDateUpdate'])) {
                    $stateAfter['installedDate'] = time();
                    unset($stateAfter['installedDateUpdate']);
                }

                if (!empty($stateAfter['enabledDateUpdate'])) {
                    $stateAfter['enabledDate'] = floor(time() / 60) * 60;
                    unset($stateAfter['enabledDateUpdate']);
                }

                if ($module) {
                    $state->remainTransitions[$key]['previous_info'] = $module->toArray();
                    if (isset($stateAfter['installed']) && $stateAfter['installed'] === false) {
                        $this->installedModulesDataSource->removeOne($module->id);

                    } else {
                        $module = $this->updateModuleRecord($module, $stateAfter);
                        if ($module->id === 'CDev-Core') {
                            $this->coreConfigDataSource->version = $module->version;
                        }

                        $this->installedModulesDataSource->saveOne($module);
                    }

                } elseif (!$module) {
                    if ($stateAfter['installed'] === true) {
                        $module = $this->marketplaceModulesDataSource->findByVersion($transition['id'], $stateAfter['version']);

                        if ($module) {
                            $module = $this->prepareInstalledModule($this->updateModuleRecord($module, $stateAfter));
                            $this->installedModulesDataSource->installModule($module);
                        } else {
                            $this->logger->critical(sprintf('Module %s was not found in marketplace data source', $transition['id']));

                            throw AbortException::fromUpdateDataSourceStepMissingMarketplaceModule($transition['id']);
                        }
                    } else {
                        $this->logger->critical(sprintf('Module %s was not found in data source', $transition['id']));

                        throw AbortException::fromUpdateDataSourceStepMissingModule($transition['id']);
                    }
                }

                if ($this->uploadedModulesDataSource->find($transition['id'])) {
                    $this->uploadedModulesDataSource->removeOne($transition['id']);
                }
            }

            $this->coreConfigDataSource->saveOne(time(), 'dataDate');
        }
    }

    private function refreshInstalledModulesDataSource(): void
    {
        $existent = $this->installedModulesDataSource->updateModulesData();
        $missing  = $this->installedModulesDataSource->removeMissedModules();

        $this->logger->info(get_class($this) . ':' . __FUNCTION__);
        $this->logger->debug(
            get_class($this) . ':' . __FUNCTION__,
            [
                'existent' => $existent,
                'missing'  => $missing,
            ]
        );
    }

    /**
     * @param Module $module
     * @param array  $stateAfter
     *
     * @return Module
     */
    private function updateModuleRecord(Module $module, $stateAfter): Module
    {
        $module->merge($stateAfter);

        return $module;
    }

    /**
     * @param Module $module
     *
     * @return Module
     */
    private function prepareInstalledModule(Module $module): Module
    {
        $module->merge($this->moduleInfoProvider->getModuleInfo($module->id));

        return $module;
    }
}
