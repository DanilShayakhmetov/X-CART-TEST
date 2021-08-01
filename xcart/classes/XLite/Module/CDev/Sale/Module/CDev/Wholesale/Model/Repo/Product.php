<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Module\CDev\Wholesale\Model\Repo;

/**
 * Product
 * @Decorator\Depend ({"CDev\Wholesale","CDev\Sale"})
 */
class Product extends \XLite\Model\Repo\Product implements \XLite\Base\IDecorator
{
    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    Count only flag
     *
     * @return void
     */
    protected function prepareCndSaleDiscount(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        parent::prepareCndSaleDiscount($queryBuilder, $value, $countOnly);

        if ($value instanceof \XLite\Module\CDev\Sale\Model\SaleDiscount
            && !$value->getApplyToWholesale()
        ) {
            $queryBuilder->linkLeft('p.wholesalePrices', 'wp')
                ->andWhere('wp IS NULL');
        }
    }
}
