<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Executor\Step\Execute;

use Psr\Log\LoggerInterface;
use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\Bus\Domain\Module;
use XCart\Bus\Exception\Rebuild\HoldException;
use XCart\Bus\Exception\RebuildException;
use XCart\Bus\Helper\HookFilter;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "redeploy", weight = "6500")
 */
class CheckPostponedHooks implements StepInterface
{
    /**
     * @var InstalledModulesDataSource
     */
    private $installedModulesDataSource;

    /**
     * @var HookFilter
     */
    private $hookFilter;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param InstalledModulesDataSource $installedModulesDataSource
     * @param HookFilter                 $hookFilter
     * @param LoggerInterface            $logger
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        InstalledModulesDataSource $installedModulesDataSource,
        HookFilter $hookFilter,
        LoggerInterface $logger
    ) {
        return new self(
            $installedModulesDataSource,
            $hookFilter,
            $logger
        );
    }

    /**
     * @param InstalledModulesDataSource $installedModulesDataSource
     * @param HookFilter                 $hookFilter
     * @param LoggerInterface            $logger
     */
    public function __construct(
        InstalledModulesDataSource $installedModulesDataSource,
        HookFilter $hookFilter,
        LoggerInterface $logger
    ) {
        $this->installedModulesDataSource = $installedModulesDataSource;
        $this->hookFilter                 = $hookFilter;
        $this->logger                     = $logger;
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return int
     */
    public function getProgressMax(ScriptState $scriptState): int
    {
        return $scriptState->isStepCompleted(UnpackPacks::class)
            ? count($this->getTransitions($scriptState))
            : count($this->filterScriptTransitions($scriptState->transitions));
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
            'currentActionInfo'   => [],
            'finishedActionInfo'  => [],
            'data'                => [
                'modulesWithHooks'    => [],
                'executeModulesHooks' => [],
            ],
            'progressMax'         => $this->getProgressMax($scriptState),
            'progressValue'       => 0,
        ]);

        $state->currentActionInfo = $this->getCurrentActionInfoMessage($state);

        return $state;
    }

    /**
     * If there are modified files after last transition processed we hold step execution
     * On release action do nothing (just check if all transitions are processed)
     *
     * @param StepState $state
     * @param string    $action
     * @param array     $params
     *
     * @return StepState
     * @throws RebuildException
     */
    public function execute(StepState $state, $action = self::ACTION_EXECUTE, array $params = []): StepState
    {
        $data = $state->data;

        switch ($action) {
            /** @noinspection PhpMissingBreakStatementInspection */
            case self::ACTION_EXECUTE:
                foreach ($state->remainTransitions as $transition) {
                    /** @var Module $module */
                    $module                     = $this->installedModulesDataSource->find($transition['id']);
                    $data['modulesWithHooks'][] = [$transition['id'], $module->authorName, $module->moduleName];
                }

                $state->data = $data;

                $state->finishedTransitions = $state->remainTransitions;
                $state->remainTransitions   = [];

                throw HoldException::fromCheckPostponedHooksStepHooksPresent($state);

                break;

            case self::ACTION_RELEASE:
                $data['executeModulesHooks'] = $params['executeModulesHooks'];

                $state->data = $data;

                break;
            case self::ACTION_SKIP_STEP:
                $state = $this->skipStep($state);

                break;
            default:
                break;
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
    private function getTransitions(ScriptState $scriptState): array
    {
        $transitions = [];

        $parentStepState = $scriptState->getCompletedStepState(CheckPacks::class);
        if ($parentStepState) {
            $transitions = $parentStepState->finishedTransitions ?: [];
        }

        if (empty($transitions)) {
            return [];
        }

        return array_filter(array_map(
            function ($transition) {
                if (!Module::isPreviuosMajorVersion($transition['version_before'], $transition['version_after'])) {
                    return [];
                }

                $hooks = $this->getHooksListByTransition($transition);
                if ($hooks) {
                    return [
                        'id' => $transition['id'],
                    ];
                }

                return [];
            },
            $this->filterScriptTransitions($transitions)
        ));
    }

    /**
     * @param array $transition
     *
     * @return array
     */
    private function getHooksListByTransition(array $transition): array
    {
        $preUpgradeHooks = $this->hookFilter->filterHooksByType(
            $transition['new_files'],
            'pre_upgrade',
            $transition['id'],
            $transition['version_before'],
            $transition['version_after']
        );

        $postUpgradeHooks = $this->hookFilter->filterHooksByType(
            $transition['new_files'],
            'post_upgrade',
            $transition['id'],
            $transition['version_before'],
            $transition['version_after']
        );

        return array_merge($preUpgradeHooks, $postUpgradeHooks);
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
                   'message' => 'rebuild.check_postponed_hooks.state',
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
                   'message' => 'rebuild.check_postponed_hooks.state.finished',
               ]]
            : [];
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
     * @param array[] $transitions
     *
     * @return array[]
     */
    private function filterScriptTransitions($transitions): array
    {
        return array_filter($transitions, static function ($transition) {
            return $transition['transition'] === ChangeUnitProcessor::TRANSITION_UPGRADE;
        });
    }
}
