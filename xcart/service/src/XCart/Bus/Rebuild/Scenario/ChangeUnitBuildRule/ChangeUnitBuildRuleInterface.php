<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\ChangeUnitBuildRule;

use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;

interface ChangeUnitBuildRuleInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param array $changeUnit
     *
     * @return bool
     */
    public function isApplicable(array $changeUnit): bool;

    /**
     * @param array $transitions
     *
     * @return bool
     */
    public function isApplicableWithOthers(array $transitions): bool;

    /**
     * @param array $changeUnit
     *
     * @return TransitionInterface|null
     */
    public function build(array $changeUnit): ?TransitionInterface;
}
