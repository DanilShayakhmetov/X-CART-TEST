<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return new \XLite\Rebuild\Hook(
    function () {
        $queryBuilder = \XLite\Core\Database::getEM()->createQueryBuilder();
        $queryBuilder->update('XLite\Model\QuickData', 'qd')
            ->set('qd.minPrice', 'qd.price')
            ->set('qd.maxPrice', 'qd.price')
            ->getQuery()
            ->execute();
    }
);
