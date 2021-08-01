<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter;

use FilterIterator;
use Iterator;
use XCart\Bus\Exception\NotImplemented;

abstract class AFilter extends FilterIterator
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $data;

    /**
     * @param Iterator $iterator
     * @param string   $field
     * @param mixed    $data
     */
    public function __construct(Iterator $iterator, $field, $data)
    {
        parent::__construct($iterator);

        $this->field = $field;
        $this->data  = $data;
    }

    /**
     * @throws NotImplemented
     */
    public function accept()
    {
        throw new NotImplemented();
    }
}
