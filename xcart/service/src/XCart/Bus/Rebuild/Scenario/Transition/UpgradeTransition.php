<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\Transition;

use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;

class UpgradeTransition extends TransitionAbstract
{
    /**
     * @return string
     */
    public function getType(): string
    {
        return ChangeUnitProcessor::TRANSITION_UPGRADE;
    }

    /**
     * @param TransitionInterface $transition
     *
     * @return bool
     */
    public function canOverwrite(TransitionInterface $transition): bool
    {
        return $transition instanceof EnableTransition
            || $transition instanceof DisableTransition;
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
                'upgraded'            => true,
                'installed'           => true,
                'version'             => $this->getVersion(),
            ]
        );
    }
}
