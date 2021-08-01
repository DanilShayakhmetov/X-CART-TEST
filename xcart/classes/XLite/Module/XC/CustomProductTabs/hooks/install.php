<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return new \XLite\Rebuild\Hook(
    function () {
        $queryBuilder = \XLite\Core\Database::getEM()->createQueryBuilder();
        $queryBuilder->update('XLite\Model\Product\GlobalTab', 'gt')
            ->set('gt.enabled', 1)
            ->getQuery()
            ->execute();

        $queryBuilder = \XLite\Core\Database::getEM()->createQueryBuilder();
        $queryBuilder->update('XLite\Module\XC\CustomProductTabs\Model\Product\Tab', 'pt')
            ->set('pt.enabled', 1)
            ->getQuery()
            ->execute();
    }
);
