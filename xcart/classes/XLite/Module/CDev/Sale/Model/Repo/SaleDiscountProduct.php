<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Model\Repo;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * SaleDiscountProduct repo
 */
class SaleDiscountProduct extends \XLite\Model\Repo\ARepo
{
    use ExecuteCachedTrait;

    const P_SALE_DISCOUNT_ID = 'sale_discount_id';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndSaleDiscountId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $value = intval($value);

        $alias = $this->getMainAlias($queryBuilder);
        $queryBuilder->andWhere($alias . '.saleDiscount = :saleDiscountId')
            ->setParameter('saleDiscountId', $value);
    }

    /**
     * @param $saleDiscountId
     * @return mixed
     */
    public function getSaleDiscountProductIds($saleDiscountId)
    {
        return $this->executeCachedRuntime(function() use ($saleDiscountId) {
            $qb = $this->createPureQueryBuilder('sdp');

            $qb->select('p.product_id')
                ->linkInner('sdp.product', 'p')
                ->andWhere('sdp.saleDiscount = :saleDiscountId')
                ->setParameter('saleDiscountId', $saleDiscountId);

            $result = $qb->getResult();

            return array_column($result, 'product_id');
        }, ['getSaleDiscountProductIds', $saleDiscountId]);
    }
}
