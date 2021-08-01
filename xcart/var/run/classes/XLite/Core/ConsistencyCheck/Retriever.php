<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ConsistencyCheck;


class Retriever
{
    /**
     * @var RuleInterface[]
     */
    protected $rules;

    /**
     * Retriever constructor.
     *
     * @param RuleInterface[] $rules
     */
    public function __construct($rules)
    {
        $this->rules = $rules;
    }

    /**
     * @return array
     */
    public function getInconsistencies()
    {
        $result = [];

        foreach ($this->rules as $name => $rule) {
            $inconsistency = $rule->execute();

            if ($inconsistency instanceof Inconsistency) {
                $result[$name] = $inconsistency;
            }
        }

        return $result;
    }
}
