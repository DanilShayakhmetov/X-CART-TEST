<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Marketplace\Validator;

use XCart\Marketplace\IValidator;

class Schema implements IValidator
{
    /**
     * @var array
     */
    private $schema;

    /**
     * @var bool
     */
    private $addEmpty;

    /**
     * @param array $schema
     * @param bool $addEmpty
     */
    public function __construct($schema, $addEmpty = true)
    {
        $this->schema = $schema;
        $this->addEmpty = $addEmpty;
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    public function isValid($data)
    {
        $filtered = filter_var_array($data, $this->schema, $this->addEmpty);

        return array_intersect_key($data, $filtered) == $filtered;
    }
}
