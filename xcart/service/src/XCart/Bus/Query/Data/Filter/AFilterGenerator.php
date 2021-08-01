<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter;

use Iterator;

abstract class AFilterGenerator
{
    /**
     * @param Iterator $iterator
     * @param string   $field
     * @param mixed    $data
     *
     * @return mixed
     */
    abstract public function __invoke(Iterator $iterator, $field, $data);
}
