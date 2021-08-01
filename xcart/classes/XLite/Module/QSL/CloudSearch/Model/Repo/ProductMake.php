<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Model\Repo;

use Doctrine\ORM\QueryBuilder;

/**
 * The "product" repo class
 *
 * @Decorator\Depend({"QSL\Make"})
 */
abstract class ProductMake extends \XLite\Model\Repo\Product implements \XLite\Base\IDecorator
{
    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed        $value        Condition data
     *
     * @return void
     */
    protected function prepareCndLevelProduct(QueryBuilder $queryBuilder, $value)
    {
        if (!$this->isLoadProductsWithCloudSearch()) {
            parent::prepareCndLevelProduct($queryBuilder, $value);
        }
    }
}