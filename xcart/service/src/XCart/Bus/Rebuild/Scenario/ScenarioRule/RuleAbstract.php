<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\ScenarioRule;

use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Query\Data\MarketplaceModulesDataSource;
use XCart\Bus\Rebuild\Scenario\ScenarioRule\ScenarioRuleInterface;
use XCart\Bus\Rebuild\Scenario\Transition\EnableTransition;
use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;
use XCart\Bus\Rebuild\Scenario\TransitionInfo;

abstract class RuleAbstract implements ScenarioRuleInterface
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
     * @param InstalledModulesDataSource   $installedModulesDataSource
     * @param MarketplaceModulesDataSource $marketplaceModulesDataSource
     */
    public function __construct(
        InstalledModulesDataSource $installedModulesDataSource,
        MarketplaceModulesDataSource $marketplaceModulesDataSource
    ) {
        $this->installedModulesDataSource   = $installedModulesDataSource;
        $this->marketplaceModulesDataSource = $marketplaceModulesDataSource;
    }

    /**
     * @param TransitionInterface $transition
     *
     * @return bool
     */
    public function isApplicable(TransitionInterface $transition): bool
    {
        return true;
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
}
