<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Rollback;

use Exception;
use GuzzleHttp\Exception\ParseException;
use Psr\Log\LoggerInterface;
use XCart\Bus\Client\XCart;
use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Domain\Module;
use XCart\Bus\Exception\Rebuild\AbortException;
use XCart\Bus\Exception\RebuildException;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\Execute\UpdateModulesList as ExecuteUpdateModulesList;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service
 * @RebuildStep(script = "rollback", weight = "4000")
 */
class UpdateModulesList implements StepInterface
{
    /**
     * @var XCart
     */
    private $client;

    /**
     * @var string
     */
    private $rebuildId;

    /**
     * @var InstalledModulesDataSource
     */
    private $installedModulesDataSource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param XCart                      $client
     * @param InstalledModulesDataSource $installedModulesDataSource
     * @param LoggerInterface            $logger
     */
    public function __construct(
        XCart $client,
        InstalledModulesDataSource $installedModulesDataSource,
        LoggerInterface $logger
    ) {
        $this->client                     = $client;
        $this->installedModulesDataSource = $installedModulesDataSource;
        $this->logger                     = $logger;
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
     * @throws RebuildException
     */
    public function execute(StepState $state, $action = self::ACTION_EXECUTE, array $params = []): StepState
    {
        $this->rebuildId = $state->rebuildId;

        $remainTransitions = $state->remainTransitions;

        if ($remainTransitions) {
            $modulesList = $this->getActualModulesList($remainTransitions);
            $result      = $this->executeUpdate($modulesList);

            $state->data = [
                'cacheId' => $result['cacheId'],
                'list'    => $modulesList,
            ];

            $state->finishedTransitions = $remainTransitions;
            $state->remainTransitions   = [];
            $state->progressValue++;
        }

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

        if ($parentScriptState->stepState->id === ExecuteUpdateModulesList::class
            || $parentScriptState->isStepCompleted(ExecuteUpdateModulesList::class)) {
            return $scriptState->transitions;
        }

        return [];
    }

    /**
     * @param StepState $state
     *
     * @return string[]
     */
    private function getCurrentActionInfoMessage(StepState $state): array
    {
        return [[
                    'message' => 'rollback.update_modules_list.state',
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
                    'message' => 'rollback.update_modules_list.state.finished',
                    'params'  => [],
                ]];
    }

    /**
     * @param array $transitions
     *
     * @return array
     */
    private function getActualModulesList(array $transitions): array
    {
        $result = [];

        foreach ($transitions as $transition) {
            [$author, $name] = Module::explodeModuleId($transition['id']);
            if (!isset($result[$author])) {
                $result[$author] = [];
            }

            if (!isset($transition['stateBeforeTransition']['installed'])
                || $transition['stateBeforeTransition']['installed'] !== false
            ) {
                $result[$author][$name] = $transition['stateBeforeTransition']['enabled'] ?? false;
            } else {
                $result[$author][$name] = false;
            }
        }

        /** @var Module $module */
        foreach ($this->installedModulesDataSource->getAll() as $module) {
            if (isset($result[$module->author][$module->name])) {
                continue;
            }

            if (!isset($result[$module->author])) {
                $result[$module->author] = [];
            }

            if (!isset($result[$module->author][$module->name])) {
                $result[$module->author][$module->name] = $module->enabled;
            }
        }

        return $result;
    }

    /**
     * @param array $list
     *
     * @return array
     *
     * @throws RebuildException
     */
    private function executeUpdate($list): array
    {
        try {
            $this->logger->debug(
                'Send actual modules list',
                [
                    'modules_list' => $list,
                ]
            );

            $response = $this->client->executeRebuildRequest(
                ['rebuildId' => $this->rebuildId],
                ['modules_list' => $list]
            );

            if (isset($response['errors'])) {
                throw AbortException::fromUpdateModulesListStepUpdateError($response['errors']);
            }

            return $response;

        } catch (ParseException $e) {
            throw AbortException::fromUpdateModulesListStepWrongResponse($e);

        } catch (Exception $e) {
            throw new AbortException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
