<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Execute;

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
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "redeploy", weight = "11000")
 */
class UpdateModulesList implements StepInterface
{
    /**
     * @var InstalledModulesDataSource
     */
    private $installedModulesDataSource;

    /**
     * @var XCart
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $rebuildId;

    /**
     * @param InstalledModulesDataSource $installedModulesDataSource
     * @param XCart                      $client
     * @param LoggerInterface            $logger
     */
    public function __construct(
        InstalledModulesDataSource $installedModulesDataSource,
        XCart $client,
        LoggerInterface $logger
    ) {
        $this->installedModulesDataSource = $installedModulesDataSource;
        $this->client                     = $client;
        $this->logger                     = $logger;
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
     * @throws RebuildException
     */
    public function execute(StepState $state, $action = self::ACTION_EXECUTE, array $params = []): StepState
    {
        $this->rebuildId = $state->rebuildId;

        $remainTransitions = $state->remainTransitions;

        $modulesList    = $this->getActualModulesList($remainTransitions);
        $integratedList = $this->getIntegratedModulesList();
        $result         = $this->executeUpdate($modulesList, $integratedList);

        $this->installedModulesDataSource->updateModulesData();

        $state->data = [
            'cacheId'        => $result['cacheId'] ?? null,
            'list'           => $modulesList,
            'integratedList' => $integratedList,
        ];

        $state->finishedTransitions = $remainTransitions;
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
        return $scriptState->type === 'install' ? [] : ($scriptState->transitions ?: []);
    }

    /**
     * @param $transitions
     *
     * @return array
     */
    private function getActualModulesList(array $transitions): array
    {
        $result = [];

        foreach ($this->installedModulesDataSource->getAll() as $module) {
            /** @var Module $module */
            if ($module->id === 'XC-Service') {
                continue;
            }

            if ($module->id === 'CDev-Core') {
                continue;
            }

            if (!isset($result[$module->author])) {
                $result[$module->author] = [];
            }

            if ($module->isSystem) {
                $module->enabled = !$GLOBALS['config']->getOption('performance', 'ignore_system_modules');
            }

            $result[$module->author][$module->name] = $module->enabled;
        }

        foreach ($transitions as $transition) {
            if ($transition['id'] === 'XC-Service') {
                continue;
            }

            [$author, $name] = explode('-', $transition['id']);
            if (!isset($result[$author])) {
                $result[$author] = [];
            }

            if ($transition['transition'] === 'remove') {
                $result[$author][$name] = 'remove';
            } else {
                $result[$author][$name] = $transition['stateAfterTransition']['enabled'];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getIntegratedModulesList(): array
    {
        $result = [];

        foreach ($this->installedModulesDataSource->getAll() as $module) {
            /** @var Module $module */
            if ($module->id === 'CDev-Core') {
                continue;
            }

            if ($module->id === 'XC-Service') {
                continue;
            }

            if ($module->integrated) {
                if (!isset($result[$module->author])) {
                    $result[$module->author] = [];
                }

                if ($module->isSystem) {
                    $module->enabled = !$GLOBALS['config']->getOption('performance', 'ignore_system_modules');
                    $result[$module->author][$module->name] = $module->enabled;
                } else {
                    $result[$module->author][$module->name] = true;
                }
            }
        }

        return $result;
    }

    /**
     * @param array $list
     * @param array $integratedList
     *
     * @return array
     * @throws RebuildException
     */
    private function executeUpdate($list, $integratedList): ?array
    {
        try {
            $this->logger->debug(
                'Send actual modules list',
                [
                    'modules_list'    => $list,
                    'integrated_list' => $integratedList,
                ]
            );

            $response = $this->client->executeRebuildRequest(
                ['rebuildId' => $this->rebuildId],
                ['modules_list' => $list, 'integrated_list' => $integratedList]
            );

            if (isset($response['errors'])) {
                $this->logger->critical(
                    'Failed to update modules list',
                    [
                        'errors' => $response['errors'],
                    ]
                );

                throw AbortException::fromUpdateModulesListStepUpdateError($response['errors']);
            }

            return $response;

        } catch (ParseException $e) {
            $this->logger->critical(
                sprintf('Failed to update modules list: %s', $e->getMessage()),
                [
                    'response' => $e->getResponse()->getBody(),
                ]
            );

            throw AbortException::fromUpdateModulesListStepWrongResponse($e);

        } catch (Exception $e) {
            $this->logger->critical(sprintf('Failed to update modules list: %s', $e->getMessage()));

            throw new AbortException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param StepState $state
     *
     * @return string[]
     */
    private function getCurrentActionInfoMessage(StepState $state): array
    {
        return $state->state !== StepState::STATE_FINISHED_SUCCESSFULLY
            ? [[
                   'message' => 'rebuild.update_modules_list.state',
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
        return $state->state === StepState::STATE_FINISHED_SUCCESSFULLY
            ? [[
                   'message' => 'rebuild.update_modules_list.state.finished',
               ]]
            : [];
    }
}
