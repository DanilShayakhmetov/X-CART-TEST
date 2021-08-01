<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use Iterator;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\Filter\AFilter;

class ExistsUpdate extends AFilter
{
    /**
     * @var Module[]
     */
    protected $modules;

    public function __construct(Iterator $iterator, $field, $data, $modules = [])
    {
        parent::__construct($iterator, $field, $data);

        $this->modules = $modules;
    }

    /**
     * @return bool
     */
    public function accept()
    {
        /** @var Module $item */
        $item = $this->getInnerIterator()->current();

        return isset($this->modules[$item->id]);
    }
}