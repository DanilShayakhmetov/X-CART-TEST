<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Serialization\Deserializer;

class SchemaRoot extends SchemaGroup
{
    /**
     * @var int
     */
    protected $index = 1;

    /**
     * SchemaRoot constructor.
     */
    public function __construct()
    {
        parent::__construct(1);
    }

    /**
     * @param mixed $prop
     *
     * @return bool
     */
    public function addProp($prop): bool
    {
        $this->props[] = is_string($prop) ? $this->parseProp($prop) : $prop;

        return true;
    }

    /**
     * @return mixed
     */
    public function getProp()
    {
        [$prop] = $this->props;

        return $prop;
    }
}