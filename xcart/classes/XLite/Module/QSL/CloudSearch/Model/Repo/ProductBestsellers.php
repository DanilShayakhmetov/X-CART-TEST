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
 * @Decorator\Depend({"CDev\Bestsellers"})
 */
abstract class ProductBestsellers extends \XLite\Model\Repo\Product implements \XLite\Base\IDecorator
{
    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $qb    Query builder to prepare
     * @param boolean      $value Condition data
     *
     * @return void
     */
    protected function prepareCndBestsellers(QueryBuilder $qb, $value)
    {
        if (!$this->isLoadProductsWithCloudSearch()) {
            parent::prepareCndBestsellers($qb, $value);
        }
    }
}
