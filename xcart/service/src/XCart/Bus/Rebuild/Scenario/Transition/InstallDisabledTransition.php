<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\Transition;

use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;

class InstallDisabledTransition extends TransitionAbstract
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return ChangeUnitProcessor::TRANSITION_INSTALL_DISABLED;
    }

    /**
     * @param array $state
     *
     * @return array
     */
    public function getStateBeforeTransition(array $state): array
    {
        if (!$state) {
            return [
                'installed'  => false,
                'integrated' => false,
                'enabled'    => false,
                'version'    => null,
            ];
        }

        return parent::getStateBeforeTransition($state);
    }

    /**
     * @param array $state
     *
     * @return array
     */
    public function getStateAfterTransition(array $state): array
    {
        return array_replace(
            $state,
            [
                'installed'           => true,
                'integrated'          => false,
                'enabled'             => false,
                'version'             => $this->getVersion(),
                'installedDateUpdate' => true,
            ]
        );
    }
}
