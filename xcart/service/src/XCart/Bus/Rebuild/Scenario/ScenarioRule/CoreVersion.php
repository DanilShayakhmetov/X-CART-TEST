<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\ScenarioRule;

use Psr\Log\LoggerInterface;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\CoreConfigDataSource;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Query\Data\MarketplaceModulesDataSource;
use XCart\Bus\Rebuild\Scenario\ScenarioBuilder;
use XCart\Bus\Rebuild\Scenario\Transition\DisableTransition;
use XCart\Bus\Rebuild\Scenario\Transition\InstallDisabledTransition;
use XCart\Bus\Rebuild\Scenario\Transition\InstallEnabledTransition;
use XCart\Bus\Rebuild\Scenario\Transition\RemoveTransition;
use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;
use XCart\Bus\Rebuild\Scenario\Transition\UpgradeTransition;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * Check minRequiredCoreVersion
 *
 * @Service\Service(arguments={"logger"="XCart\Bus\Core\Logger\Rebuild"})
 */
class CoreVersion extends RuleAbstract
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
     * @var LoggerInterface
     */
    private $logger;

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
        parent::__construct($installedModulesDataSource, $marketplaceModulesDataSource);

        $this->coreConfigDataSource = $coreConfigDataSource;
        $this->logger               = $logger;
    }

    /**
     * @param TransitionInterface $transition
     *
     * @return bool
     */
    public function isApplicable(TransitionInterface $transition): bool
    {
        if ($transition instanceof DisableTransition || $transition instanceof RemoveTransition) {
            return false;
        }

        return $transition->getModuleId() !== 'CDev-Core';
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

        $id     = $transition->getModuleId();
        $module = $this->getModule($transition);

        $requiredMajorCoreVersion = $this->getMajorFormattedVersion($module->version);
        $requiredMinorCoreVersion = $module->minorRequiredCoreVersion ?? '0.0.0.0';

        if ($requiredMajorCoreVersion > $majorCoreVersionFormatted
            || ($requiredMajorCoreVersion === $majorCoreVersionFormatted
                && version_compare($minorCoreVersionFormatted, $requiredMinorCoreVersion, '<')
            )
        ) {
            throw ScenarioRuleException::fromCoreVersionCoreUpgradeRequired($id);
        }

        if ($requiredMajorCoreVersion < $majorCoreVersionFormatted) {
            $this->logger->warning(sprintf('Trying to magane outdated module: %s (%s), core (%s)', $id, $module->version, $coreVersion));

            throw ScenarioRuleException::fromCoreVersionModuleUpgradeRequired($id);
        }
    }

    /**
     * @param TransitionInterface $transition
     * @param ScenarioBuilder     $scenarioBuilder
     */
    public function applyTransform(TransitionInterface $transition, ScenarioBuilder $scenarioBuilder): void
    {
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
     * @param TransitionInterface $transition
     *
     * @return Module
     */
    private function getModule(TransitionInterface $transition): Module
    {
        $id = $transition->getModuleId();

        if ($transition instanceof InstallEnabledTransition
            || $transition instanceof InstallDisabledTransition
            || $transition instanceof UpgradeTransition
        ) {
            $version = $transition->getVersion();

            return $this->getMarketplaceModule($id, $version);
        }

        return $this->getInstalledModule($id);
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
