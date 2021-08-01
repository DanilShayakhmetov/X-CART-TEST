<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter;

use XCart\Bus\Domain\Module;

class Simple extends AFilter
{
    /**
     * @return bool
     */
    public function accept()
    {
        /** @var Module $item */
        $item = $this->getInnerIterator()->current();
        $field = $this->field;

        return isset($item->{$field}) && $item->{$field} === $this->data;
    }
}
