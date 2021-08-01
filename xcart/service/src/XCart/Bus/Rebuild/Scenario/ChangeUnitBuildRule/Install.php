<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\ChangeUnitBuildRule;

use XCart\Bus\Query\Data\Flatten\Flatten;
use XCart\Bus\Query\Data\ModulesDataSource;
use XCart\Bus\Rebuild\Scenario\Transition\InstallDisabledTransition;
use XCart\Bus\Rebuild\Scenario\Transition\InstallEnabledTransition;
use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class Install implements ChangeUnitBuildRuleInterface
{
    /**
     * @var ModulesDataSource
     */
    private $modulesDataSource;

    /**
     * @param ModulesDataSource $modulesDataSource
     */
    public function __construct(
        ModulesDataSource $modulesDataSource
    ) {
        $this->modulesDataSource = $modulesDataSource;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'install';
    }

    /**
     * @param array $changeUnit
     *
     * @return bool
     */
    public function isApplicable(array $changeUnit): bool
    {
        if (isset($changeUnit['install'])
            && $changeUnit['install'] === true
            && (!empty($changeUnit['version']) || !empty($changeUnit['installLatestVersion']))
        ) {
            $module = $this->modulesDataSource->findOne($changeUnit['id'], Flatten::RULE_LAST, ['canInstall' => true], $changeUnit['replaceData'] ?? []);

            return $module && $module->installedVersion === null;
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
        return true;
    }

    /**
     * @param array $changeUnit
     *
     * @return TransitionInterface|null
     */
    public function build(array $changeUnit): ?TransitionInterface
    {
        $id      = $changeUnit['id'];
        $enabled = isset($changeUnit['enable'])
            ? (bool) $changeUnit['enable']
            : true;

        if (!empty($changeUnit['installLatestVersion'])) {
            $module = $this->modulesDataSource->findOne($id, Flatten::RULE_LAST, ['canInstall' => true]);

            $changeUnit['version'] = $module->version;
        }

        return $enabled
            ? new InstallEnabledTransition($id, $changeUnit['version'])
            : new InstallDisabledTransition($id, $changeUnit['version']);
    }
}
