<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Execute;

use Doctrine\Common\Cache\CacheProvider;
use Exception;
use Psr\Log\LoggerInterface;
use Silex\Application;
use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Exception\Rebuild\HoldException;
use XCart\Bus\Exception\RebuildException;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\SilexAnnotations\Annotations\Service;
use XCart\SilexAnnotations\AnnotationServiceProvider;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "self-upgrade", weight = "6000")
 */
class ReloadPage implements StepInterface
{
    /**
     * @var CacheProvider
     */
    private $cacheProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Application     $app
     * @param LoggerInterface $logger
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        LoggerInterface $logger
    ) {
        return new self(
            $app[AnnotationServiceProvider::CACHE_SERVICE_NAME],
            $logger
        );
    }

    /**
     * @param CacheProvider   $cacheProvider
     * @param LoggerInterface $logger
     */
    public function __construct(
        CacheProvider $cacheProvider,
        LoggerInterface $logger
    ) {
        $this->logger        = $logger;
        $this->cacheProvider = $cacheProvider;
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
        $this->logger->info(get_class($this) . ':' . __FUNCTION__);

        $state = new StepState([
            'id'                  => static::class,
            'state'               => StepState::STATE_INITIALIZED,
            'rebuildId'           => $scriptState->id,
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
        if ($action === self::ACTION_EXECUTE) {
            $this->cacheProvider->flushAll();

            $this->logger->debug('Request page reloading');

            throw HoldException::fromReloadPageStepReload($state);
        }

        $this->logger->debug(sprintf('Page reloaded'));

        $state->state = StepState::STATE_FINISHED_SUCCESSFULLY;

        $state->currentActionInfo  = $this->getCurrentActionInfoMessage($state);
        $state->finishedActionInfo = $this->getFinishedActionInfoMessage($state);

        $state->progressValue++;

        return $state;
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
                   'message' => 'rebuild.reload_page.state',
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
                   'message' => 'rebuild.reload_page.state.finished',
               ]]
            : [];
    }
}
