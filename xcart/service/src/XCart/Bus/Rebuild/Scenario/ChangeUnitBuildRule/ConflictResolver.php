<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Rebuild\Scenario\ChangeUnitBuildRule;

use XCart\Bus\Rebuild\Scenario\Transition\TransitionInterface;

class ConflictResolver
{
    /**
     * @param ChangeUnitBuildRuleInterface[] $rules
     * @param TransitionInterface[]          $transitions
     *
     * @return array
     */
    public function resolve(array $rules, array $transitions): array
    {
        return array_filter($transitions, static function ($ruleName) use ($rules, $transitions) {
            $rule = $rules[$ruleName];

            return $rule->isApplicableWithOthers($transitions);
        }, ARRAY_FILTER_USE_KEY);
    }
}
