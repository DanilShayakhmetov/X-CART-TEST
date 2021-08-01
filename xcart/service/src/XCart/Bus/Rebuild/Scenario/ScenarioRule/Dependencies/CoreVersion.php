<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\ScenarioRule\Dependencies;

use Psr\Log\LoggerInterface;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Query\Data\MarketplaceModulesDataSource;
use XCart\Bus\Rebuild\Scenario\ScenarioBuilder;
use XCart\Bus\Rebuild\Scenario\ScenarioRule\ScenarioRuleException;
use XCart\Bus\Rebuild\Scenario\Transition\EnableTransition;
use XCart\Bus\Rebuild\Scenario\Transition\InstallDisabledTransition;
use XCart\Bus\Rebuild\Scenario\Transition\InstallEnabledTransition;
use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;
use XCart\Bus\Rebuild\Scenario\Transition\UpgradeTransition;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * Check minRequiredCoreVersion
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 */
class CoreVersion extends DependencyRuleAbstract
{
    /**
     * @var string
     */
    private $coreVersion;

    /**
     * @var CoreConfigDataSource
     */
    private $coreConfigDataSource;

    /**
     * @param InstalledModulesDataSource   $installedModulesDataSource
     * @param MarketplaceModulesDataSource $marketplaceModulesDataSource
     * @param CoreConfigDataSource         $coreConfigDataSource
     * @param LoggerInterface              $logger
     */
    public function __construct(
        InstalledModulesDataSource $installedModulesDataSource,
        MarketplaceModulesDataSource $marketplaceModulesDataSource,
        CoreConfigDataSource $coreConfigDataSource,
        LoggerInterface $logger
    ) {
        parent::__construct($installedModulesDataSource, $marketplaceModulesDataSource, $logger);

        $this->coreConfigDataSource = $coreConfigDataSource;
    }

    /**
     * @param TransitionInterface $transition
     * @param ScenarioBuilder     $scenarioBuilder
     *
     * @throws ScenarioRuleException
     */
    public function applyFilter(TransitionInterface $transition, ScenarioBuilder $scenarioBuilder): void
    {
        $coreTransition = $scenarioBuilder->getTransition('CDev-Core');
        if ($coreTransition) {
            $coreVersion = $coreTransition->getVersion() ?: $this->getCoreVersion();
        } else {
            $coreVersion = $this->getCoreVersion();
        }

        $majorCoreVersionFormatted = $this->getMajorFormattedVersion($coreVersion);
        $minorCoreVersionFormatted = $this->getMinorFormattedVersion($coreVersion);

        foreach ($this->getDependencies($transition) as $dependency) {
            $id = $this->getDependencyId($dependency);

            $dependencyModule = $this->getDependencyModule($id, $scenarioBuilder);

            if ($dependencyModule) {
                $requiredMajorCoreVersion = $this->getMajorFormattedVersion($dependencyModule->version);
                $requiredMinorCoreVersion = $dependencyModule->minorRequiredCoreVersion ?? '0.0.0.0';

                if ($requiredMajorCoreVersion > $majorCoreVersionFormatted
                    || ($requiredMajorCoreVersion === $majorCoreVersionFormatted
                        && version_compare($minorCoreVersionFormatted, $requiredMinorCoreVersion, '<')
                    )
                ) {
                    throw ScenarioRuleException::fromDependenciesCoreVersionCoreUpgradeRequired($transition->getModuleId(), $id);
                }

                if ($requiredMajorCoreVersion < $majorCoreVersionFormatted) {
                    throw ScenarioRuleException::fromDependenciesCoreVersionModuleUpgradeRequired($transition->getModuleId(), $id);
                }
            } else {
                if ($transition instanceof EnableTransition || $transition instanceof InstallEnabledTransition) {
                    throw ScenarioRuleException::fromDependenciesCoreVersionNonExistentModule($transition->getModuleId(), $id);
                }
            }
        }
    }

    /**
     * @param TransitionInterface $transition
     * @param ScenarioBuilder     $scenarioBuilder
     *
     * @throws \Exception
     */
    public function applyTransform(TransitionInterface $transition, ScenarioBuilder $scenarioBuilder): void
    {
    }

    /**
     * @param string          $id
     * @param ScenarioBuilder $scenarioBuilder
     *
     * @return Module|null
     */
    private function getDependencyModule($id, ScenarioBuilder $scenarioBuilder)
    {
        $transition = $scenarioBuilder->getTransition($id);
        if ($transition) {
            $version = null;

            if ($transition instanceof InstallEnabledTransition
                || $transition instanceof InstallDisabledTransition
                || $transition instanceof UpgradeTransition
            ) {
                $version = $transition->getVersion();
            }

            return $this->getMarketplaceModule($id, $version);
        }

        return $this->getInstalledModule($id);
    }

    /**
     * @return string
     */
    private function getCoreVersion(): string
    {
        if (empty($this->coreVersion)) {
            $this->coreVersion = $this->coreConfigDataSource->version;
        }

        return $this->coreVersion;
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

    /**
     * @param string $version
     *
     * @return string
     */
    private function getMinorFormattedVersion($version): string
    {
        [, , $minor, $build] = Module::explodeVersion($version);

        return $minor . '.' . $build;
    }
}
