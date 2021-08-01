<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Serialization\Deserializer;

class SchemaClassObject extends SchemaGroup
{
    /**
     * @var string
     */
    protected $className;

    /**
     * SchemaClassObject constructor.
     *
     * @param string      $buffer
     * @param SchemaGroup $parent
     */
    public function __construct(string $buffer, SchemaGroup $parent = null)
    {
        $data = explode(':', $buffer, 4);

        $count           = (int) $data[3];
        $this->className = substr($data[2], 1, -1);

        parent::__construct($count, $parent);
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }
}