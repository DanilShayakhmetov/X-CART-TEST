<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use Iterator;
use XCart\Bus\Core\Annotations\DataSourceFilter;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\Filter\AFilter;

/**
 * @DataSourceFilter(name="tags")
 */
class Tags extends AFilter
{
    /**
     * @var array
     */
    private $tags;

    /**
     * @param Iterator $iterator
     * @param string   $field
     * @param mixed    $data
     */
    public function __construct(Iterator $iterator, $field, $data)
    {
        parent::__construct($iterator, $field, $data);

        $this->tags = array_map('strtolower', $data);
    }

    /**
     * @return bool
     */
    public function accept()
    {
        /** @var Module $item */
        $item = $this->getInnerIterator()->current();

        return (bool) array_intersect($this->tags, array_map('strtolower', $item->tags));
    }
}
