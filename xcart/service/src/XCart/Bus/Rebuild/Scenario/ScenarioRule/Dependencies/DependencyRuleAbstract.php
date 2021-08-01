<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\ScenarioRule\Dependencies;

use Psr\Log\LoggerInterface;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Query\Data\MarketplaceModulesDataSource;
use XCart\Bus\Rebuild\Scenario\ScenarioRule\ScenarioRuleInterface;
use XCart\Bus\Rebuild\Scenario\Transition\EnableTransition;
use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;
use XCart\Bus\Rebuild\Scenario\TransitionInfo;

abstract class DependencyRuleAbstract implements ScenarioRuleInterface
{
    /**
     * @var InstalledModulesDataSource
     */
    protected $installedModulesDataSource;

    /**
     * @var MarketplaceModulesDataSource
     */
    protected $marketplaceModulesDataSource;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param InstalledModulesDataSource   $installedModulesDataSource
     * @param MarketplaceModulesDataSource $marketplaceModulesDataSource
     * @param LoggerInterface              $logger
     */
    public function __construct(
        InstalledModulesDataSource $installedModulesDataSource,
        MarketplaceModulesDataSource $marketplaceModulesDataSource,
        LoggerInterface $logger
    ) {
        $this->installedModulesDataSource   = $installedModulesDataSource;
        $this->marketplaceModulesDataSource = $marketplaceModulesDataSource;
        $this->logger                       = $logger;
    }

    /**
     * @param TransitionInterface $transition
     *
     * @return bool
     */
    public function isApplicable(TransitionInterface $transition): bool
    {
        return (bool) $this->getDependencies($transition);
    }

    /**
     * @param TransitionInterface $transition
     *
     * @return string[]
     */
    protected function getDependencies(TransitionInterface $transition): array
    {
        if ($transition instanceof EnableTransition) {
            return $this->getInstalledDependencies(
                $transition->getModuleId()
            );
        }

        return $this->getMarketplaceDependencies(
            $transition->getModuleId(),
            $transition->getVersion()
        );
    }

    /**
     * @param string $id
     * @param string $version
     *
     * @return string[]
     */
    protected function getMarketplaceDependencies($id, $version): array
    {
        $module = $this->getMarketplaceModule($id, $version);

        return $module->dependsOn ?? [];
    }

    /**
     * @param string $id
     *
     * @return string[]
     */
    protected function getInstalledDependencies($id): array
    {
        $module = $this->getInstalledModule($id);

        return $module->dependsOn ?? [];
    }

    /**
     * @param string $id
     *
     * @return Module|null
     */
    protected function getInstalledModule($id): ?Module
    {
        return $this->installedModulesDataSource->find($id);
    }

    /**
     * @param string      $id
     * @param string|null $version
     *
     * @return Module|null
     */
    protected function getMarketplaceModule($id, $version = null): ?Module
    {
        return $this->marketplaceModulesDataSource->findByVersion($id, $version);
    }

    /**
     * @param array|string $dependency
     *
     * @return string|null
     */
    protected function getDependencyId($dependency): ?string
    {
        if (is_string($dependency)) {
            return Module::convertModuleId($dependency);
        }

        if (is_array($dependency)) {
            return $dependency['id'];
        }

        return null;
    }

    /**
     * @param string $id
     *
     * @return Module[]
     */
    protected function getIncompatibleModules($id): array
    {
        $currentModule = $this->getInstalledModule($id);

        $mainIncompatible = (array) ($currentModule->incompatibleWith ?? []);

        foreach ($mainIncompatible as $key => $mainIncompatibleItem) {
            $mainIncompatible[$key] = $this->getInstalledModule($mainIncompatibleItem);
        }

        /** @var Module[] $modules */
        $modules = $this->installedModulesDataSource->getAll();

        $additionalIncompatible = array_filter($modules, function ($module) use ($id) {
            /** @var Module $module */
            $incompatibleModules = (array) ($module->incompatibleWith ?? []);

            foreach ($incompatibleModules as $incompatibleModule) {
                if ($this->getDependencyId($incompatibleModule) === $id) {
                    return true;
                }
            }

            return false;
        });

        return array_filter(
            array_merge($mainIncompatible, $additionalIncompatible)
        );
    }

    /**
     * @param TransitionInterface $transition
     */
    protected function fillTransitionInfo(TransitionInterface $transition): void
    {
        $info = new TransitionInfo();
        $info->setReason('rule');
        $info->setReasonHuman('Transition added by rule');

        $transition->setInfo($info);
    }
}
