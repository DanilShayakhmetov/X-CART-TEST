<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ConsistencyCheck;

use XLite\Model\AEntity;

/**
 * Trait DefaultModelPrinter
 * @package XLite\Core\ConsistencyCheck
 */
trait DefaultModelStringifier
{
    /**
     * @param AEntity $item
     *
     * @return string
     */
    public function stringifyModel(AEntity $item)
    {
        return (string)$item->getUniqueIdentifier();
    }
}
