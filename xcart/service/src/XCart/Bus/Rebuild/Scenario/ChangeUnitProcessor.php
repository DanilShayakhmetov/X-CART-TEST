<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario;

use Exception;
use Psr\Log\LoggerInterface;
use Silex\Application;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Query\Data\MarketplaceModulesDataSource;
use XCart\Bus\Rebuild\Scenario\ChangeUnitBuildRule\ConflictResolver;
use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 */
class ChangeUnitProcessor
{
    const TRANSITION_INSTALL_ENABLED  = 'install_enabled';
    const TRANSITION_INSTALL_DISABLED = 'install_disabled';
    const TRANSITION_ENABLE           = 'enable';
    const TRANSITION_DISABLE          = 'disable';
    const TRANSITION_REMOVE           = 'remove';
    const TRANSITION_UPGRADE          = 'upgrade';

    /**
     * @var InstalledModulesDataSource
     */
    private $installedModulesDataSource;

    /**
     * @var TransitionBuilder
     */
    private $transitionBuilder;

    /**
     * @var ScenarioBuilder
     */
    private $scenarioBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param InstalledModulesDataSource   $installedModulesDataSource
     * @param MarketplaceModulesDataSource $marketplaceModulesDataSource
     * @param ScenarioBuilder              $scenarioBuilder
     * @param LoggerInterface              $logger
     *
     * @return static
     *
     * @Service\Constructor
     * @codeCoverageIgnore
     */
    public static function serviceConstructor(
        Application $app,
        InstalledModulesDataSource $installedModulesDataSource,
        ScenarioBuilder $scenarioBuilder,
        LoggerInterface $logger
    ) {
        // todo: use annotations
        return new self(
            $installedModulesDataSource,
            new TransitionBuilder(
                [
                    $app[ChangeUnitBuildRule\Enable::class],
                    $app[ChangeUnitBuildRule\Install::class],
                    $app[ChangeUnitBuildRule\Remove::class],
                    $app[ChangeUnitBuildRule\Upgrade::class],
                ],
                new ConflictResolver(),
                $logger
            ),
            $scenarioBuilder,
            $logger
        );
    }

    /**
     * @param InstalledModulesDataSource $installedModulesDataSource
     * @param TransitionBuilder          $transitionBuilder
     * @param ScenarioBuilder            $scenarioBuilder
     * @param LoggerInterface            $logger
     */
    public function __construct(
        InstalledModulesDataSource $installedModulesDataSource,
        TransitionBuilder $transitionBuilder,
        ScenarioBuilder $scenarioBuilder,
        LoggerInterface $logger
    ) {
        $this->installedModulesDataSource = $installedModulesDataSource;
        $this->transitionBuilder          = $transitionBuilder;
        $this->scenarioBuilder            = $scenarioBuilder;
        $this->logger                     = $logger;
    }

    /**
     * @param array|null $scenario
     * @param array      $changeUnits
     *
     * @return array
     * @throws ScenarioRule\ScenarioRuleException
     * @throws Exception
     */
    public function process(array $scenario, array $changeUnits): array
    {
        if (!$changeUnits) {
            $tempScenario       = $this->fillModulesTransitions([], $changeUnits);
            $modulesTransitions = !empty($tempScenario['modulesTransitions'])
                ? $tempScenario['modulesTransitions']
                : [];

            return $scenario + ['modulesTransitions' => $modulesTransitions];
        }

        $scenario['modulesTransitions'] = [];

        if (isset($scenario['changeUnits'])) {
            $changeUnits = $this->mergeChangeUnits(
                $scenario['changeUnits'], $changeUnits
            );
        }

        $changeUnits = $this->indexChangeUnits($changeUnits);

        $changeUnits = $this->sortChangeUnits($changeUnits);

        $transitions = $this->buildTransitionsFromChangeUnits($changeUnits);

        $this->logger->debug(
            'Process scenario',
            [
                'changeUnits' => $changeUnits,
                'transitions' => array_map(static function ($transition) {
                    is_object($transition) ? get_class($transition) : '';
                }, $transitions),
            ]
        );

        foreach ($transitions as $id => $transition) {
            if ($transition === null) {
                $this->scenarioBuilder->removeTransition($id);
            }
        }

        $this->scenarioBuilder->addSystemTransitions();

        foreach ($transitions as $id => $transition) {
            if ($transition) {
                $this->scenarioBuilder->addTransition($transition);
            }
        }

        $scenario              = $this->fillModulesTransitions($scenario, $changeUnits);
        $scenario['updatedAt'] = time();

        return $scenario;
    }

    /**
     * @param array $scenario
     * @param array $changeUnits
     *
     * @return array
     * @throws ScenarioRule\ScenarioRuleException
     */
    private function fillModulesTransitions(array $scenario, array $changeUnits): array
    {
        $transitions = $this->scenarioBuilder->getTransitions();

        $changeUnitsResult = [];
        foreach ($transitions as $transition) {
            $id = $transition->getModuleId();
            if (isset($changeUnits[$id])) {
                $changeUnitsResult[$id] = $changeUnits[$id];
            }
            $scenario['modulesTransitions'][$id] = $this->convertTransitionIntoType($transition);
        }

        $scenario['changeUnits'] = $changeUnitsResult;

        return $scenario;
    }

    /**
     * @param array $old
     * @param array $new
     *
     * @return array
     */
    private function mergeChangeUnits(array $old, array $new): array
    {
        $oldChangeUnits = $this->indexChangeUnits($old);
        $changeUnits    = $this->indexChangeUnits($new);

        return array_merge(
            $oldChangeUnits,
            $changeUnits
        );
    }

    /**
     * @param array $units
     *
     * @return array
     */
    private function indexChangeUnits(array $units): array
    {
        $ids = array_map(static function ($unit) {
            return $unit['id'];
        }, $units);

        return array_combine(
            $ids,
            $units
        );
    }

    /**
     * @param array $changeUnits
     *
     * @return array
     * @throws Exception
     */
    private function buildTransitionsFromChangeUnits($changeUnits): array
    {
        $transitions = [];
        foreach ($changeUnits as $changeUnit) {
            $transitions[$changeUnit['id']] = $this->transitionBuilder->build($changeUnit);
        }

        return $transitions;
    }

    /**
     * @param TransitionInterface $transition
     *
     * @return array
     */
    private function convertTransitionIntoType(TransitionInterface $transition): array
    {
        $info = $transition->getInfo();
        /** @var Module $module */
        $module     = $this->installedModulesDataSource->find($transition->getModuleId()) ?: [];
        $moduleData = $module ? $module->toArray() : [];

        return [
            'id'                    => $transition->getModuleId(),
            'transition'            => $transition->getType(),
            'stateBeforeTransition' => $transition->getStateBeforeTransition($moduleData),
            'stateAfterTransition'  => $transition->getStateAfterTransition($moduleData),
            'info'                  => [
                'reason'      => $info ? $info->getReason() : '',
                'humanReason' => $info ? $info->getReasonHuman() : '',
            ],
        ];
    }

    /**
     * @param array $changeUnits
     *
     * @return array
     */
    private function sortChangeUnits($changeUnits): array
    {
        uasort($changeUnits, function ($a, $b) {
            /** @var Module $am */
            $am = $this->installedModulesDataSource->find($a['id']);
            /** @var Module $bm */
            $bm = $this->installedModulesDataSource->find($b['id']);

            if (in_array($b['id'], $am->dependsOn ?? [], true)) {
                return !empty($a['enable']) ? 1 : -1;
            }

            if (in_array($a['id'], $bm->dependsOn ?? [], true)) {
                return !empty($b['enable']) ? -1 : 1;
            }

            return 0;
        });

        return $changeUnits;
    }
}
