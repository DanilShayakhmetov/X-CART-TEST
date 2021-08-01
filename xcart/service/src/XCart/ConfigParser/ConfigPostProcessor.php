<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\ConfigParser;

class ConfigPostProcessor
{
    /**
     * @var callable[]
     */
    private $rules;

    /**
     * @param callable[] $rules
     */
    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }

    /**
     * @param callable $rule
     */
    public function addRule($rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * @param array[] $data
     *
     * @return array[]
     */
    public function process($data)
    {
        foreach ($this->rules as $rule) {
            if (is_callable($rule)) {
                $data = $rule($data);
            }
        }

        return $data;
    }
}
