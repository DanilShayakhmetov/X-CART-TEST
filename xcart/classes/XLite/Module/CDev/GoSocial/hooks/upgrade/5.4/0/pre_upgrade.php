<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function saveOgMetaProducts54()
{
    $qb     = \XLite\Core\Database::getRepo('XLite\Model\Product')->createPureQueryBuilder();
    $alias  = $qb->getMainAlias();
    $data   = $qb->select("$alias.product_id as id, $alias.ogMeta as og")
        ->andWhere("$alias.useCustomOG = :useCustomOG")
        ->setParameter('useCustomOG', true)
        ->getQuery()
        ->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    $handle = fopen(LC_DIR_TMP . 'products_og_meta.csv', 'w');
    foreach ($data as $datum) {
        if (strlen(trim($datum['og']))) {
            fputcsv($handle, $datum);
        }
    }
}

function saveOgMetaCategory54()
{
    $qb     = \XLite\Core\Database::getRepo('XLite\Model\Category')->createPureQueryBuilder();
    $alias  = $qb->getMainAlias();
    $data   = $qb->select("$alias.category_id as id, $alias.ogMeta as og")
        ->andWhere("$alias.useCustomOG = :useCustomOG")
        ->setParameter('useCustomOG', true)
        ->getQuery()
        ->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
    $handle = fopen(LC_DIR_TMP . 'categories_og_meta.csv', 'w');
    foreach ($data as $datum) {
        if (strlen(trim($datum['og']))) {
            fputcsv($handle, $datum);
        }
    }
}

function saveOgMetaPage54()
{
    if (\XLite\Core\Database::getRepo('XLite\Model\Module')->isModuleEnabled('CDev\\SimpleCMS')) {
        $qb     = \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Page')->createPureQueryBuilder();
        $alias  = $qb->getMainAlias();
        $data   = $qb->select("$alias.id as id, $alias.ogMeta as og")
            ->andWhere("$alias.useCustomOG = :useCustomOG")
            ->setParameter('useCustomOG', true)
            ->getQuery()
            ->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        $handle = fopen(LC_DIR_TMP . 'pages_og_meta.csv', 'w');
        foreach ($data as $datum) {
            if (strlen(trim($datum['og']))) {
                fputcsv($handle, $datum);
            }
        }
    }
}

return function () {
    saveOgMetaProducts54();
    saveOgMetaCategory54();
    saveOgMetaPage54();
};