<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\Transition;

use XCart\Bus\Rebuild\Scenario\ChangeUnitProcessor;

class RemoveTransition extends TransitionAbstract
{
    /**
     * @param TransitionInterface $transition
     *
     * @return bool
     */
    public function canOverwrite(TransitionInterface $transition): bool
    {
        return $transition instanceof DisableTransition;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return ChangeUnitProcessor::TRANSITION_REMOVE;
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
                'installed' => true,
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
                'installed'  => false,
                'integrated' => false,
                'enabled'    => false,
            ]
        );
    }
}
