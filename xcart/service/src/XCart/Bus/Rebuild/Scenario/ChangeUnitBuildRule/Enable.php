<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\ChangeUnitBuildRule;

use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Rebuild\Scenario\Transition\DisableTransition;
use XCart\Bus\Rebuild\Scenario\Transition\EnableTransition;
use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class Enable implements ChangeUnitBuildRuleInterface
{
    /**
     * @var InstalledModulesDataSource
     */
    private $installedModulesDataSource;

    /**
     * @param InstalledModulesDataSource $installedModulesDataSource
     */
    public function __construct(
        InstalledModulesDataSource $installedModulesDataSource
    ) {
        $this->installedModulesDataSource = $installedModulesDataSource;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'enable';
    }

    /**
     * @param array $changeUnit
     *
     * @return bool
     */
    public function isApplicable(array $changeUnit): bool
    {
        if (isset($changeUnit['enable'])) {
            /** @var Module $module */
            $module = $this->installedModulesDataSource->find($changeUnit['id']);

            return $module && ($changeUnit['enable'] || $module->canDisable);
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
        return !isset($transitions['install']) && !isset($transitions['remove']);
    }

    /**
     * @param array $changeUnit
     *
     * @return TransitionInterface|null
     */
    public function build(array $changeUnit): ?TransitionInterface
    {
        $id = $changeUnit['id'];
        $enable = (bool) $changeUnit['enable'];

        if ($enable !== $this->getEnabledState($id)) {
            return $enable ? new EnableTransition($id) : new DisableTransition($id);
        }

        return null;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    private function getEnabledState($id): bool
    {
        /** @var Module|null $module */
        $module = $this->installedModulesDataSource->find($id);

        return $module->enabled ?? false;
    }
}
