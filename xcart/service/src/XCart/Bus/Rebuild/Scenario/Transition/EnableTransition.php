<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\Transition;

use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;

class EnableTransition extends TransitionAbstract
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return ChangeUnitProcessor::TRANSITION_ENABLE;
    }

    /**
     * @param array $state
     *
     * @return array
     */
    public function getStateBeforeTransition(array $state): array
    {
        return array_replace(
            [
                'installed'  => true,
                'integrated' => true,
                'enabled'    => false,
            ],
            $state
        );
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
                'integrated'        => true,
                'enabled'           => true,
                'enabledDateUpdate' => true,
            ]
        );
    }
}
