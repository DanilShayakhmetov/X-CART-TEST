<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Query\Data\Filter\Module;

use XCart\Bus\Core\Annotations\DataSourceFilter;
use XCart\Bus\Domain\Module;
use XCart\Bus\Query\Data\Filter\AFilter;

/**
 * @DataSourceFilter(name="payable")
 */
class Payable extends AFilter
{
    /**
     * @return bool
     */
    public function accept()
    {
        /** @var Module $item */
        $item = $this->getInnerIterator()->current();

        switch ($this->data) {
            case 'paid':
                return $item->price > 0;

            case 'free':
                return $item->price === 0.0;

            case 'sale':
                return $item->onSale;
        }

        return true;
    }
}
