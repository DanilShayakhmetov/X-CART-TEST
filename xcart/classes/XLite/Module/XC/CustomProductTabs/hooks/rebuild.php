<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return new \XLite\Rebuild\Hook(
    function () {
        $qb = \XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab')->createQueryBuilder();

        $alias = $qb->getMainAlias();
        $qb->addSelect('COUNT(psa.id) as HIDDEN aliases_count')
            ->leftJoin("{$alias}.product_specific_aliases", 'psa')
            ->andWhere("{$alias}.service_name IS NOT NULL")
            ->having('aliases_count < :products_count')
            ->groupBy("{$alias}.id")
            ->setParameter('products_count', \XLite\Core\Database::getRepo('XLite\Model\Product')->count());

        foreach ($qb->getResult() as $globalTab) {
            \XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab')->createGlobalTabAliases($globalTab);
        }

        \XLite\Module\XC\CustomProductTabs\Main::removeUninstalledModulesTabs();
    }
);
