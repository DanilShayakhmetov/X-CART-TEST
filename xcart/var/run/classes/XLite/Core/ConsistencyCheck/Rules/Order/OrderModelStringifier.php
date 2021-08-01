<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ConsistencyCheck\Rules\Order;

use XLite\Model\Order;

/**
 * Trait OrderModelStringifier
 * @package XLite\Core\ConsistencyCheck
 */
trait OrderModelStringifier
{
    /**
     * @param Order $item
     *
     * @return string
     */
    public function stringifyModel(Order $item)
    {
        return \XLite\Core\Translation::getInstance()->translate('Order') . ' ' . $item->getPrintableOrderNumber();
    }
}
