<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return new \XLite\Rebuild\Hook(
    function () {
        $tablePrefix        = \XLite\Core\Database::getInstance()->getTablePrefix();
        $globalTabTableName = $tablePrefix . 'global_product_tabs';
        $customTabTableName = $tablePrefix . 'custom_global_tabs';

        if (\XLite\Core\Database::getEM()->getConnection()->getSchemaManager()->tablesExist([$customTabTableName, $globalTabTableName])) {
            $sql = "DELETE gt FROM `$globalTabTableName` as gt LEFT JOIN `$customTabTableName` as ct ON gt.id = ct.global_tab_id WHERE ct.id IS NOT NULL";
            \XLite\Core\Database::getEM()->getConnection()->executeQuery($sql);
        }
    }
);
