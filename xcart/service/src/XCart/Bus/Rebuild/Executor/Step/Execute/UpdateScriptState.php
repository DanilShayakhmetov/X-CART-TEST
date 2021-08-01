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
use XCart\Bus\Domain\ModuleInfoProvider;
use XCart\Bus\Helper\UrlBuilder;
use XCart\Bus\Rebuild\Executor\ScriptState;
use XCart\Bus\Rebuild\Executor\Step\StepInterface;
use XCart\Bus\Rebuild\Executor\StepState;
use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 * @RebuildStep(script = "redeploy", weight = "10000")
 */
class UpdateScriptState implements StepInterface
{
    /**
     * @var ModuleInfoProvider
     */
    private $moduleInfoProvider;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ModuleInfoProvider   $moduleInfoProvider
     * @param CoreConfigDataSource $coreConfigDataSource
     * @param UrlBuilder           $urlBuilder
     * @param LoggerInterface      $logger
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        ModuleInfoProvider $moduleInfoProvider,
        UrlBuilder $urlBuilder,
        LoggerInterface $logger
    ) {
        return new static(
            $moduleInfoProvider,
            $urlBuilder,
            $logger
        );
    }

    /**
     * @param ModuleInfoProvider   $moduleInfoProvider
     * @param CoreConfigDataSource $coreConfigDataSource
     * @param UrlBuilder           $urlBuilder
     * @param LoggerInterface      $logger
     */
    public function __construct(
        ModuleInfoProvider $moduleInfoProvider,
        UrlBuilder $urlBuilder,
        LoggerInterface $logger
    ) {
        $this->logger             = $logger;
        $this->urlBuilder         = $urlBuilder;
        $this->moduleInfoProvider = $moduleInfoProvider;
    }

    /**
     * @param ScriptState $scriptState
     *
     * @return int
     */
    public function getProgressMax(ScriptState $scriptState): int
    {
        return (int) (bool) ($scriptState->isStepCompleted(UnpackPacks::class)
            ? $this->getTransitions($scriptState)
            : $scriptState->transitions);
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

        $requiredSkins = [];
        foreach ($transitions as $id => $transition) {
            $moduleInfo = $this->moduleInfoProvider->getModuleInfo($id, $this->getModulePath($id, $scriptState));
            if (
                !$moduleInfo
                || $moduleInfo['type'] !== 'skin'
                || $transition['transition'] !== ChangeUnitProcessor::TRANSITION_ENABLE
            ) {
                continue;
            }

            $requiredSkins = array_merge($requiredSkins, $moduleInfo['dependsOn']);
        }
        $requiredSkins = array_unique($requiredSkins);

        $recentSkins = [];
        foreach ($transitions as $id => $transition) {
            $moduleInfo = $this->moduleInfoProvider->getModuleInfo($id, $this->getModulePath($id, $scriptState));
            if (!$moduleInfo) {
                continue;
            }

            if ($transition['transition'] === ChangeUnitProcessor::TRANSITION_INSTALL_ENABLED
                && $moduleInfo['type'] === 'skin'
                && !in_array($id, $requiredSkins)
            ) {
                $recentSkins[]                  = $id;
                $transitions[$id]['transition'] = ChangeUnitProcessor::TRANSITION_INSTALL_DISABLED;

                $transitions[$id]['stateAfterTransition']['integrated'] = false;
                $transitions[$id]['stateAfterTransition']['enabled']    = false;
            }

            if ($transition['transition'] === ChangeUnitProcessor::TRANSITION_UPGRADE) {
                if (($this->getMajorFormattedVersion($transition['stateBeforeTransition']['version'])
                        !== $this->getMajorFormattedVersion($transition['stateAfterTransition']['version']))
                    && !isset($transitions['CDev-Core'])
                ) {
                    $transitions[$id]['stateAfterTransition']['integrated'] = true;
                    $transitions[$id]['stateAfterTransition']['enabled']    = true;
                }
            }
        }

        $scriptState->transitions = $transitions;

        if (count($recentSkins) === count($transitions)) {
            $scriptState->returnUrl = $this->urlBuilder->buildAdminUrl()
                . '?target=layout&'
                . http_build_query(['recent' => $recentSkins]);
        }

        $this->logger->debug(
            sprintf('Update script transitions'),
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
     */
    public function execute(StepState $state, $action = self::ACTION_EXECUTE, array $params = []): StepState
    {
        $remainTransitions = $state->remainTransitions;

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
        return $scriptState->transitions ?: [];
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
                   'message' => 'rebuild.update_script_state.state',
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
                   'message' => 'rebuild.update_script_state.state.finished',
                   'params'  => [$finished, $total],
               ]]
            : [];
    }

    /**
     * @param string      $id
     * @param ScriptState $scriptState
     *
     * @return string
     */
    private function getModulePath($id, ScriptState $scriptState): string
    {
        $parentStepState = $scriptState->getCompletedStepState(UnpackPacks::class);
        if ($parentStepState && isset($parentStepState->finishedTransitions[$id])) {
            return $parentStepState->finishedTransitions[$id]['pack_dir'];
        }

        return '';
    }

    /**
     * @param string $version
     *
     * @return string
     */
    private function getMajorFormattedVersion($version): string
    {
        [$system, $major, ,] = Module::explodeVersion($version);

        return $system . '.' . $major;
    }
}
