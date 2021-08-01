<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Serialization\Deserializer;

class SchemaReference
{
    /**
     * @var int
     */
    protected $index;

    /**
     * @var SchemaRoot
     */
    protected $root;

    /**
     * SchemaReference constructor.
     *
     * @param int        $index
     */
    public function __construct(int $index)
    {
        $this->index = $index;
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }
}