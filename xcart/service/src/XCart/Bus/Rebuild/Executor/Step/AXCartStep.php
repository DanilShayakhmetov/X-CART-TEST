<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step;

use Exception;
use GuzzleHttp\Exception\ParseException;
use Psr\Log\LoggerInterface;
use XCart\Bus\Client\XCart;
use XCart\Bus\Client\XCartCLI;
use XCart\Bus\Exception\Rebuild\AbortException;
use XCart\Bus\Exception\RebuildException;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\StepState;

abstract class AXCartStep implements StepInterface
{
    /**
     * @var XCart
     */
    protected $client;

    /**
     * @var XCartCLI
     */
    protected $cliClient;

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
            'remainTransitions'   => $this->getTransitions($scriptState),
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

        try {
            $state = $this->processTransition($state);
        } catch (RebuildException $exception) {
            $this->logger->critical(
                sprintf('Run XCart step error: %s', $exception->getMessage()),
                [
                    'data' => $exception->getData(),
                ]
            );

            throw $exception;
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
    abstract protected function getTransitions(ScriptState $scriptState): array;

    /**
     * @param ScriptState $scriptState
     *
     * @return string|null
     */
    abstract protected function getCacheId(ScriptState $scriptState): ?string;

    /**
     * @param StepState $state
     *
     * @return string[]
     */
    private function getCurrentActionInfoMessage(StepState $state): array
    {
        $remainTransitions = $state->remainTransitions;
        if (empty($remainTransitions)) {
            return [];
        }

        $transition = current($remainTransitions);

        return [[
                    'message' => 'rebuild.xcart_step.' . $transition . '.state',
                    'params'  => [$transition],
                ]];
    }

    /**
     * @param StepState $state
     *
     * @return string[]
     */
    private function getFinishedActionInfoMessage(StepState $state): array
    {
        $result = [];

        foreach ($state->finishedTransitions as $transition) {
            $result[] = [
                'message' => 'rebuild.xcart_step.' . $transition . '.state.finished',
                'params'  => [$transition],
            ];
        }

        return $result;
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
        $id                  = key($remainTransitions);

        try {
            $this->logger->debug(sprintf('Execute X-Cart step: %s', $transition));

            if (PHP_SAPI === 'cli') {
                $result = $this->cliClient->executeRebuildStep(
                    $transition,
                    $this->rebuildId,
                    $state->data['cacheId']
                );
            } else {
                $result = $this->client->executeRebuildStep(
                    $transition,
                    $this->rebuildId,
                    $state->data['cacheId']
                );
            }

        } catch (ParseException $exception) {
            throw AbortException::fromXCartStepWrongResponseFormat($exception);

        } catch (Exception $exception) {
            throw AbortException::fromXCartStepWrongResponse($exception);
        }

        if (!$result) {
            throw AbortException::fromXCartStepEmptyResponse();
        }

        if (isset($result['errors']) && $result['errors']) {
            throw AbortException::fromXCartStepErrorResponse($transition, $result['errors']);
        }

        if (isset($result['state']) && $result['state'] === 'finished') {
            // move transition from remain to finished
            $finishedTransitions[$id] = $transition;
            unset($remainTransitions[$id]);
            $state->progressValue++;

        } else {
            // save updated transition to remain
            $remainTransitions[$id] = $transition;
        }

        // todo: check for 'warning' field present in response
        if (isset($result['warnings']) && $result['warnings']) {
            $this->logger->warning(
                'X-Cart step warnings',
                [
                    'warnings' => $result['warnings'],
                ]
            );

            $state->warnings = $result['warnings'];
        }

        $state->remainTransitions   = $remainTransitions;
        $state->finishedTransitions = $finishedTransitions;

        return $state;
    }
}
