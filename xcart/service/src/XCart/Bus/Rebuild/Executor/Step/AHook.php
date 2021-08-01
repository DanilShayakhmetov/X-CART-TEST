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
use XCart\Bus\Helper\HookFilter;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\StepState;

abstract class AHook implements StepInterface
{
    /**
     * @var HookFilter
     */
    protected $hookFilter;

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
     * @param HookFilter      $hookFilter
     * @param XCart           $client
     * @param XCartCLI        $cliClient
     * @param LoggerInterface $logger
     */
    public function __construct(
        HookFilter $hookFilter,
        XCart $client,
        XCartCLI $cliClient,
        LoggerInterface $logger
    ) {
        $this->hookFilter = $hookFilter;
        $this->client     = $client;
        $this->cliClient  = $cliClient;
        $this->logger     = $logger;
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
     * @return string
     */
    abstract protected function getType(): string;

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
     * @param array $transition
     *
     * @return array
     */
    abstract protected function getHooksListByTransition(array $transition): array;

    /**
     * @param array[] $transitions
     *
     * @return array[]
     */
    abstract protected function filterScriptTransitions(array $transitions): array;

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
        $transition          = current($state->remainTransitions);
        $id                  = $transition['id'];

        $progressValue = $state->progressValue;

        $cacheId = $state->data['cacheId'] ?? null;

        // main action
        $transition = $this->runTransitionHooks($transition, $cacheId);

        // update state
        if (empty($transition['remain_hooks'])) {
            $finishedTransitions[$id] = $transition;
            unset($remainTransitions[$id]);
            $progressValue++;

        } else {
            $remainTransitions[$id] = $transition;
        }

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
    protected function runTransitionHooks($transition, $cacheId)
    {
        $hook = current($transition['remain_hooks']);
        if (empty($hook)) {
            return $transition;
        }
        $hookId = key($transition['remain_hooks']);

        if (!is_array($hook)) {
            $hook = [
                'file'  => $hook,
                'state' => [],
            ];
        }

        $this->logger->info(sprintf('Run hook: %s', $hook['file']));

        try {
            $hook['state'] = $this->runHook($transition['pack_dir'] . $hook['file'], $hook['state'], $cacheId);
        } catch (RebuildException $exception) {
            $this->logger->critical(
                sprintf('Run hook error: %s', $exception->getMessage()),
                [
                    'data' => $exception->getData(),
                ]
            );

            throw $exception;
        }

        if (!empty($hook['state']['finished'])) {
            $transition['finished_hooks'][$hookId] = $hook;
            unset($transition['remain_hooks'][$hookId]);

        } else {
            $transition['remain_hooks'][$hookId] = $hook;
        }

        return $transition;
    }

    /**
     * @param string $file
     * @param array  $state
     * @param string $cacheId
     *
     * @return array
     * @throws RebuildException
     */
    protected function runHook($file, array $state, $cacheId): array
    {
        try {
            $response = $this->executeHook($file, $state, $cacheId);

        } catch (ParseException $e) {
            throw AbortException::fromHookStepWrongResponseFormat($file, $e);

        } catch (Exception $e) {
            throw AbortException::fromHookStepWrongResponse($file, $e);
        }

        if (!$response) {
            throw AbortException::fromHookStepEmptyResponse();
        }

        if (!empty($response['errors'])) {
            throw AbortException::fromHookStepErrorResponse($file, $response['errors']);
        }

        $state                = $response['hookState'] ?? $state;
        $state['initialized'] = $response['initialized'] ?? false;

        if ($response['state'] === 'finished') {
            $state['finished'] = true;
        }

        return $state;
    }

    /**
     * @param string $file
     * @param array  $state
     * @param string $cacheId
     *
     * @return mixed|null
     */
    protected function executeHook($file, $state, $cacheId)
    {
        if (PHP_SAPI === 'cli') {
            return $this->cliClient->executeHook($file, $state, $this->rebuildId, $cacheId);
        }

        return $this->client->executeHook($file, $state, $this->rebuildId, $cacheId);
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
     * @return string[]
     */
    private function getCurrentActionInfoMessage(StepState $state): array
    {
        $finished = count($state->finishedTransitions);
        $total    = $finished + count($state->remainTransitions);

        return $total !== $finished
            ? [[
                   'message' => 'rebuild.hook.' . $this->getType() . '.state',
                   'params'  => [$finished, $total, $this->getType()],
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
                   'message' => 'rebuild.hook.' . $this->getType() . '.state.finished',
                   'params'  => [$finished, $total, $this->getType()],
               ]]
            : [];
    }
}
