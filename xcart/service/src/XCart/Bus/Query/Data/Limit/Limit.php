<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Limit;

use Iterator;
use LimitIterator;

class Limit extends LimitIterator
{
    /**
     * @param Iterator $iterator
     * @param array    $limit
     */
    public function __construct(Iterator $iterator, $limit)
    {
        $count = $limit[0] ?? -1;

        if (isset($limit[1])) {
            $offset = $count;
            $count  = $limit[1];

        } else {
            $offset = 0;
        }

        parent::__construct($iterator, $offset, $count);
    }

    public function rewind(): void
    {
        try {
            parent::rewind();
        } catch (\OutOfBoundsException $e) {
        }
    }
}
