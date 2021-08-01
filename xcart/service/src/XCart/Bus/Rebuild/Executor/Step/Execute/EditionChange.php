<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Execute;

use Psr\Log\LoggerInterface;
use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "redeploy", weight = "99000")
 */
class EditionChange implements StepInterface
{
    /**
     * @var CoreConfigDataSource
     */
    private $coreConfigDataSource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CoreConfigDataSource $coreConfigDataSource
     * @param LoggerInterface      $logger
     */
    public function __construct(
        CoreConfigDataSource $coreConfigDataSource,
        LoggerInterface $logger
    ) {
        $this->logger               = $logger;
        $this->coreConfigDataSource = $coreConfigDataSource;
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
        $editionNameAfter = isset($scriptState->storeMetadata['editionName'])
            ? $scriptState->storeMetadata['editionName']
            : null;

        $stepData = [
            'editionNameBefore' => $this->coreConfigDataSource->currentEdition,
            'editionNameAfter'  => $editionNameAfter,
        ];

        $this->logger->info(get_class($this) . ':' . __FUNCTION__);
        $this->logger->debug(
            get_class($this) . ':' . __FUNCTION__,
            [
                'stepData' => $stepData,
            ]
        );

        $state = new StepState([
            'id'                  => static::class,
            'state'               => StepState::STATE_INITIALIZED,
            'rebuildId'           => $scriptState->id,
            'remainTransitions'   => [],
            'finishedTransitions' => [],
            'progressMax'         => $this->getProgressMax($scriptState),
            'progressValue'       => 0,
            'data'                => $stepData,
        ]);

        return $state;
    }

    /**
     * @param StepState $state
     * @param string    $action
     * @param array     $params
     *
     * @return StepState
     */
    public function execute(StepState $state, $action = self::ACTION_EXECUTE, array $params = []): StepState
    {
        if (!empty($state->data['editionNameAfter'])) {
            $this->coreConfigDataSource->currentEdition = $state->data['editionNameAfter'];
        }

        $state->state         = StepState::STATE_FINISHED_SUCCESSFULLY;
        $state->progressValue = 1;

        return $state;
    }
}
