<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Rollback;

use Psr\Log\LoggerInterface;
use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "rollback", weight = "99000")
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
        $this->coreConfigDataSource = $coreConfigDataSource;
        $this->logger               = $logger;
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
        $steps = $scriptState->parentState->completedSteps;

        $editionNameBefore = '';
        foreach ($steps as $step) {
            if ($step->id === \XCart\Bus\Rebuild\Executor\Step\Execute\EditionChange::class) {
                $editionNameBefore = $step->data['editionNameBefore'] ?? '';

                break;
            }
        }

        $stepData = [
            'editionNameBefore' => $editionNameBefore,
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

        $state->currentActionInfo = $this->getCurrentActionInfoMessage($state);

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
        if (!empty($state->data['editionNameBefore'])) {
            $this->coreConfigDataSource->currentEdition = $state->data['editionNameBefore'];
        }
        $state->state         = StepState::STATE_FINISHED_SUCCESSFULLY;
        $state->progressValue = 1;

        $state->currentActionInfo  = $this->getCurrentActionInfoMessage($state);
        $state->finishedActionInfo = $this->getFinishedActionInfoMessage($state);

        return $state;
    }

    /**
     * @param StepState $state
     *
     * @return string[]
     */
    private function getCurrentActionInfoMessage(StepState $state): array
    {
        return [[
                    'message' => 'rollback.edition_change.state',
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
                    'message' => 'rollback.edition_change.state.finished',
                    'params'  => [],
                ]];
    }
}
