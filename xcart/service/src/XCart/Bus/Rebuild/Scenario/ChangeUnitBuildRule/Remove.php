<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\ChangeUnitBuildRule;

use XCart\Bus\Query\Data\InstalledModulesDataSource;
use XCart\Bus\Rebuild\Scenario\Transition\RemoveTransition;
use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class Remove implements ChangeUnitBuildRuleInterface
{
    /**
     * @var InstalledModulesDataSource
     */
    protected $installedModulesDataSource;

    /**
     * @param InstalledModulesDataSource $installedModulesDataSource
     */
    public function __construct(InstalledModulesDataSource $installedModulesDataSource)
    {
        $this->installedModulesDataSource = $installedModulesDataSource;
    }

    /**
     * @return mixed
     */
    public function getName(): string
    {
        return 'remove';
    }

    /**
     * @param array $changeUnit
     *
     * @return bool
     */
    public function isApplicable(array $changeUnit): bool
    {
        return isset($changeUnit['remove'])
            && $changeUnit['remove'] === true
            && $this->installedModulesDataSource->find($changeUnit['id']);
    }

    /**
     * @param array $transitions
     *
     * @return bool
     */
    public function isApplicableWithOthers(array $transitions): bool
    {
        return true;
    }

    /**
     * @param array $changeUnit
     *
     * @return TransitionInterface|null
     */
    public function build(array $changeUnit): ?TransitionInterface
    {
        return new RemoveTransition($changeUnit['id']);
    }
}
