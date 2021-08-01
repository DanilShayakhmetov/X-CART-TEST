<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Rollback;

use Exception;
use Psr\Log\LoggerInterface;
use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Domain\Module;
use XCart\Bus\Exception\RebuildException;
use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Query\Data\MarketplaceModulesDataSource;
use XCart\Bus\Query\Data\SetDataSource;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\Execute\UpdateDataSource as ExecuteUpdateDataSource;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "rollback", weight = "6000")
 * @RebuildStep(script = "self-rollback", weight = "2000")
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
     * @var SetDataSource
     */
    private $setDataSource;

    /**
     * @var CoreConfigDataSource
     */
    private $coreConfigDataSource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param InstalledModulesDataSource   $installedModulesDataSource
     * @param MarketplaceModulesDataSource $marketplaceModulesDataSource
     * @param SetDataSource                $setDataSource
     * @param CoreConfigDataSource         $coreConfigDataSource
     * @param LoggerInterface              $logger
     */
    public function __construct(
        InstalledModulesDataSource $installedModulesDataSource,
        MarketplaceModulesDataSource $marketplaceModulesDataSource,
        SetDataSource $setDataSource,
        CoreConfigDataSource $coreConfigDataSource,
        LoggerInterface $logger
    ) {
        $this->installedModulesDataSource   = $installedModulesDataSource;
        $this->marketplaceModulesDataSource = $marketplaceModulesDataSource;
        $this->setDataSource                = $setDataSource;
        $this->logger                       = $logger;
        $this->coreConfigDataSource         = $coreConfigDataSource;
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return int
     */
    public function getProgressMax(ScriptState $scriptState): int
    {
        return (int) (bool) $this->getTransitions($scriptState);
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

        $this->coreConfigDataSource->dataDate  = 0;
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
        $parentScriptState = $scriptState->parentState;

        return $parentScriptState->isStepCompleted(ExecuteUpdateDataSource::class)
            ? $parentScriptState->getCompletedStepState(ExecuteUpdateDataSource::class)->finishedTransitions
            : [];
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
                   'message' => 'rollback.update_data_source.state',
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
                   'message' => 'rollback.update_data_source.state.finished',
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
            foreach ($state->remainTransitions as $transition) {
                /** @var Module $module */
                $module     = $this->installedModulesDataSource->find($transition['id']);
                $stateAfter = $transition['stateBeforeTransition'];

                if ($module) {
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
                    if (!empty($stateAfter['installed'])) {
                        //$module = $this->marketplaceModulesDataSource->findByVersion($transition['id'], $stateAfter['version']);
                        $module = new Module($transition['previous_info']);

                        if ($module) {
                            $module = $this->prepareInstalledModule($this->updateModuleRecord($module, $stateAfter));
                            $this->installedModulesDataSource->installModule($module);
                        } else {
                            throw new RebuildException('Module ' . $transition['id'] . ' was not found in marketplace data source');
                        }
                    }
                }

                $this->logger->debug(sprintf('Data updated: %s', $transition['id']));
            }
        }
    }

    /**
     * @param Module $module
     * @param array  $stateAfter
     *
     * @return module
     */
    private function updateModuleRecord(Module $module, array $stateAfter): Module
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
        if (!isset($module->dependsOn)) {
            $module->dependsOn = [];
        }

        return $module;
    }
}
