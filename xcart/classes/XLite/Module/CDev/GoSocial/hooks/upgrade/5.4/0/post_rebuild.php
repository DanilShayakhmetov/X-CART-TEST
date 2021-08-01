<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function restoreProductsOgMeta54($state)
{
    $path = LC_DIR_TMP . 'products_og_meta.csv';
    if (file_exists($path)) {
        $handle = fopen($path, 'r');

        if ($state) {
            fseek($handle, $state);
        }

        $i = 0;

        $qb = \XLite\Core\Database::getRepo('XLite\Model\ProductTranslation')
            ->createPureQueryBuilder();
        $al = $qb->getMainAlias();
        $qb->update();

        while (($line = fgetcsv($handle)) && $i < 50) {
            if ($line[0] && $line[1]) {
                $qb->where("$al.owner = :product")
                    ->set("$al.ogMeta", ':content')
                    ->setParameter('product', (int) $line[0])
                    ->setParameter('content', $line[1])
                    ->getQuery()
                    ->execute();
                $i++;
            }
        }

        if (feof($handle)) {
            return null;
        }

        return ftell($handle);
    }

    return null;
}

function restoreCategoriesOgMeta54($state)
{
    $path = LC_DIR_TMP . 'categories_og_meta.csv';
    if (file_exists($path)) {
        $handle = fopen($path, 'r');

        if ($state) {
            fseek($handle, $state);
        }

        $i = 0;

        $qb = \XLite\Core\Database::getRepo('XLite\Model\CategoryTranslation')
            ->createPureQueryBuilder();
        $al = $qb->getMainAlias();
        $qb->update();

        while (($line = fgetcsv($handle)) && $i < 50) {
            if ($line[0] && $line[1]) {
                $qb->where("$al.owner = :category")
                    ->set("$al.ogMeta", ':content')
                    ->setParameter('category', (int) $line[0])
                    ->setParameter('content', $line[1])
                    ->getQuery()
                    ->execute();
                $i++;
            }
        }

        if (feof($handle)) {
            return null;
        }

        return ftell($handle);
    }

    return null;
}

function restorePagesOgMeta54($state)
{
    if (\Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('CDev-SimpleCMS')) {
        $path = LC_DIR_TMP . 'pages_og_meta.csv';
        if (file_exists($path)) {
            $handle = fopen($path, 'r');

            if ($state) {
                fseek($handle, $state);
            }

            $i = 0;

            $qb = \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\PageTranslation')
                ->createPureQueryBuilder();
            $al = $qb->getMainAlias();
            $qb->update();

            while (($line = fgetcsv($handle)) && $i < 50) {
                if ($line[0] && $line[1]) {
                    $qb->where("$al.owner = :page")
                        ->set("$al.ogMeta", ':content')
                        ->setParameter('page', (int) $line[0])
                        ->setParameter('content', $line[1])
                        ->getQuery()
                        ->execute();
                    $i++;
                }
            }

            if (feof($handle)) {
                return null;
            }

            return ftell($handle);
        }
    }

    return null;
}

return function ($state) {
    if ($state) {
        if ($state === true) {
            $state = [1, 0];
        }
        $process = function (array $state) {
            switch ($state[0]) {
                case 1:
                    return restoreProductsOgMeta54($state[1]);
                case 2:
                    return restoreCategoriesOgMeta54($state[1]);
                case 3:
                    return restorePagesOgMeta54($state[1]);
            }

            return null;
        };

        $result = $process($state);

        return $result
            ? [$state[0], $result]
            : (
            $state[0] < 3
                ? [$state[0] + 1, 0]
                : null
            );
    } else {
        // Loading data to the database from yaml file
        $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';

        if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
            \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
        }

        \XLite\Core\Database::getEM()->flush();

        return true;
    }
};
