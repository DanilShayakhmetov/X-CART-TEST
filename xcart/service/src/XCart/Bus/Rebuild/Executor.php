<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild;

use Psr\Log\LoggerInterface;
use XCart\Bus\Exception\ScriptExecutionError;
use XCart\Bus\Rebuild\Executor\Script\ScriptInterface;
use XCart\Bus\Rebuild\Executor\ScriptFactory;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild","token"="x_cart.bus.user_token"})
 */
class Executor
{
    const STATE_EXECUTION_TTL = 60;

    /**
     * @var ScriptFactory
     */
    private $scriptFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $token;

    /**
     * @param ScriptFactory $scriptFactory
     * @param string        $token
     */
    public function __construct(
        ScriptFactory $scriptFactory,
        LoggerInterface $logger,
        $token
    ) {
        $this->scriptFactory = $scriptFactory;
        $this->logger        = $logger;
        $this->token         = $token;
    }

    /**
     * Initializes the scenario execution, generates step script (user for common executions)
     *
     * @param string $type
     * @param array  $scenario
     *
     * @return ScriptState
     * @throws ScriptExecutionError
     */
    public function initializeByScenario($type, array $scenario): ScriptState
    {
        $script = $this->scriptFactory->createScript($type);
        if (!$script) {
            throw ScriptExecutionError::fromUnknownScript($type);
        }

        $state = $script->initializeByTransitions($scenario['id'], $scenario['modulesTransitions']);

        if (!empty($scenario['store_metadata'])) {
            $state->storeMetadata = $scenario['store_metadata'];
        }

        $state->touch($this->token);

        if (!empty($scenario['returnUrl'])) {
            $state->returnUrl = $scenario['returnUrl'];
        }

        return $state;
    }

    /**
     * Initializes the scenario execution, generates step script (used for rollback)
     *
     * @param string      $type
     * @param ScriptState $state
     *
     * @return ScriptState
     * @throws ScriptExecutionError
     */
    public function initializeByState($type, ScriptState $state): ScriptState
    {
        $script = $this->scriptFactory->createScript($type);
        if (!$script) {
            throw ScriptExecutionError::fromUnknownScript($type);
        }

        $state = $script->initializeByState($state->id, $state);

        $state->touch($this->token);

        return $state;
    }

    /**
     * Handles the result of the script execution with given state
     *
     * @param ScriptState $state
     * @param string      $action
     * @param array       $params
     *
     * @return ScriptState
     * @throws ScriptExecutionError
     */
    public function execute(ScriptState $state, $action, array $params = []): ScriptState
    {
        $script = $this->scriptFactory->createScript($state->type);
        if (!$script) {
            throw ScriptExecutionError::fromUnknownScript($state->type);
        }

        if (!$this->canBeExecutedByCurrentUser($script, $state)) {
            $this->logger->notice('The process is owned by another user');

            throw ScriptExecutionError::fromNotOwnedProcess();
        }

        // @todo: proof of concept
        if (in_array($action, [
            StepInterface::ACTION_IGNORE,
            StepInterface::ACTION_RETRY,
            StepInterface::ACTION_RELEASE,
        ], true)) {
            $state->errorType        = null;
            $state->errorData        = null;
            $state->errorTitle       = null;
            $state->errorDescription = null;
            $state->state            = ScriptState::STATE_IN_PROGRESS;
        }

        if ($script->canAcceptState($state)) {
            $state = $script->execute(clone $state, $action, $params);
            $state->touch($this->token);

        } else {
            $this->logger->error('This script state cannot be executed further');
            $this->logger->debug(
                'This script state cannot be executed further',
                [
                    'state' => $state,
                ]
            );

            throw ScriptExecutionError::fromUnacceptableStateExecution();
        }

        return $state;
    }

    /**
     * unused
     *
     * @param ScriptState $state
     *
     * @return ScriptState
     * @throws ScriptExecutionError
     */
    public function cancel(ScriptState $state)
    {
        $script = $this->scriptFactory->createScript($state['type']);

        if (!$this->canBeExecutedByCurrentUser($script, $state)) {
            throw ScriptExecutionError::fromNotOwnedProcess();
        }

        $state = $script->cancel($state);
        $state->touch($this->token);

        return $state;
    }

    /**
     * @param ScriptInterface $script
     * @param ScriptState     $state
     *
     * @return bool
     */
    protected function canBeExecutedByCurrentUser(ScriptInterface $script, ScriptState $state): bool
    {
        return !$script->isOwnerLocked()
            || $this->token === $state->token
            || ($state->lastModifiedTime + static::STATE_EXECUTION_TTL) < time();
    }
}
