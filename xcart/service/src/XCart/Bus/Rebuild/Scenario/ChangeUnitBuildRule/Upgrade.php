<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\ChangeUnitBuildRule;

use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Query\Data\MarketplaceModulesDataSource;
use XCart\Bus\Rebuild\Scenario\Transition\DisableTransition;
use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;
use XCart\Bus\Rebuild\Scenario\Transition\UpgradeTransition;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class Upgrade implements ChangeUnitBuildRuleInterface
{
    /**
     * @var InstalledModulesDataSource
     */
    private $installedModulesDataSource;

    /**
     * @var MarketplaceModulesDataSource
     */
    private $marketplaceModulesDataSource;

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
     * @return string
     */
    public function getName(): string
    {
        return 'upgrade';
    }

    /**
     * @param array $changeUnit
     *
     * @return bool
     * @todo: check for hooks present
     */
    public function isApplicable(array $changeUnit): bool
    {
        if ((!empty($changeUnit['upgrade']) || !empty($changeUnit['install']))
            && !empty($changeUnit['version'])
        ) {
            /** @var Module $installedModule */
            $installedModule = $this->installedModulesDataSource->find($changeUnit['id']);

            if ($installedModule) {
                if (!empty($changeUnit['isUploadedAddon'])) {
                    return true;

                } elseif (version_compare($installedModule->version, $changeUnit['version'], '<')) {
                    return (bool) $this->marketplaceModulesDataSource->findByVersion($changeUnit['id'], $changeUnit['version']);
                }
            }
        }

        return false;
    }

    /**
     * @param array $transitions
     *
     * @return bool
     */
    public function isApplicableWithOthers(array $transitions): bool
    {
        $install      = isset($transitions['install']);
        $toBeDisabled = isset($transitions['enable'])
            && $transitions['enable'] instanceof DisableTransition;

        return !$install && !$toBeDisabled;
    }

    /**
     * @param array $changeUnit
     *
     * @return TransitionInterface|null
     */
    public function build(array $changeUnit): ?TransitionInterface
    {
        return new UpgradeTransition($changeUnit['id'], $changeUnit['version']);
    }
}
