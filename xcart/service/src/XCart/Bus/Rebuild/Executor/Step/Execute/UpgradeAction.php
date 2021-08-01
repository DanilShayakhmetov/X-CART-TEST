<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Execute;

use Exception;
use Psr\Log\LoggerInterface;
use XCart\Bus\Client\XCart;
use XCart\Bus\Client\XCartCLI;
use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Exception\Rebuild\AbortException;
use XCart\Bus\Exception\RebuildException;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "redeploy", weight = "16500")
 */
class UpgradeAction implements StepInterface
{
    /**
     * @var XCart
     */
    private $client;

    /**
     * @var XCartCLI
     */
    private $cliClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $rebuildId;

    /**
     * @param XCart           $client
     * @param XCartCLI        $cliClient
     * @param LoggerInterface $logger
     */
    public function __construct(
        XCart $client,
        XCartCLI $cliClient,
        LoggerInterface $logger
    ) {
        $this->client    = $client;
        $this->cliClient = $cliClient;
        $this->logger    = $logger;
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

        $cacheId = $this->getCacheId($scriptState);

        $this->logger->info(get_class($this) . ':' . __FUNCTION__);
        $this->logger->debug(
            get_class($this) . ':' . __FUNCTION__,
            [
                'cacheId'     => $cacheId,
                'transitions' => $transitions,
            ]
        );

        $state = new StepState([
            'id'                  => static::class,
            'state'               => StepState::STATE_INITIALIZED,
            'rebuildId'           => $scriptState->id,
            'remainTransitions'   => $transitions,
            'finishedTransitions' => [],
            'data'                => ['cacheId' => $cacheId],
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

        if ($action === self::ACTION_EXECUTE) {
            $state = $this->processTransition($state);
        }

        $state->state = !empty($state->remainTransitions)
            ? StepState::STATE_IN_PROGRESS
            : StepState::STATE_FINISHED_SUCCESSFULLY;

        $state->currentActionInfo  = $this->getCurrentActionInfoMessage($state);
        $state->finishedActionInfo = $this->getFinishedActionInfoMessage($state);

        return $state;
    }

    /**
     * @param StepState $state
     *
     * @return StepState
     * @throws RebuildException
     */
    protected function processTransition(StepState $state): StepState
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
        $cacheId    = $state->data['cacheId'];
        $transition = $this->runUpgradeActoin($transition, $cacheId);

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
     * @param array  $transition
     * @param string $cacheId
     *
     * @return mixed
     * @throws RebuildException
     */
    protected function runUpgradeActoin($transition, $cacheId)
    {
        $this->logger->debug(sprintf('Run upgrade action: %s', $transition['id']));

        try {
            $this->runAction($transition['id'], $cacheId);
        } catch (RebuildException $exception) {
            $this->logger->critical(
                sprintf('Upgrade action error: %s', $exception->getMessage()),
                [
                    'data' => $exception->getData(),
                ]
            );

            throw $exception;
        }

        return $transition;
    }

    /**
     * @param string $transitionId
     * @param string $cacheId
     *
     * @return bool
     * @throws RebuildException
     */
    protected function runAction($transitionId, $cacheId): bool
    {
        try {
            $response = $this->executeAction($transitionId, $cacheId);

        } catch (ParseException $e) {
            throw AbortException::fromUpgradeActionStepWrongResponseFormat($file, $e);

        } catch (Exception $e) {
            throw AbortException::fromUpgradeActionStepWrongResponse($file, $e);
        }

        if (!$response) {
            throw AbortException::fromUpgradeActionStepEmptyResponse();
        }

        if (!empty($response['errors'])) {
            throw AbortException::fromUpgradeActionStepErrorResponse($file, $response['errors']);
        }

        return true;
    }

    /**
     * @param string $file
     * @param string $cacheId
     *
     * @return mixed|null
     */
    protected function executeAction($transitionId, $cacheId)
    {
        if (PHP_SAPI === 'cli') {
            return $this->cliClient->executeAction('upgrade', ['moduleId' => $transitionId], $this->rebuildId, $cacheId);
        }

        return $this->client->executeAction('upgrade', ['moduleId' => $transitionId], $this->rebuildId, $cacheId);
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return array
     */
    protected function getTransitions(ScriptState $scriptState): array
    {
        $transitions = $scriptState->transitions;

        return array_filter($scriptState->transitions, static function ($transition) {
            return $transition['transition'] === ChangeUnitProcessor::TRANSITION_UPGRADE && $transition['stateAfterTransition']['enabled'];
        });
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return string|null
     */
    protected function getCacheId(ScriptState $scriptState): ?string
    {
        $parentStepState = $scriptState->getCompletedStepState(UpdateModulesList::class);

        return $parentStepState ? $parentStepState->data['cacheId'] : null;
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
                   'message' => 'rebuild.action.upgrade.state',
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
                   'message' => 'rebuild.action.upgrade.state.finished',
                   'params'  => [$finished, $total],
               ]]
            : [];
    }
}
