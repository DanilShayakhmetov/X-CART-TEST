<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Model\Repo;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Coupon products repo
 */
class CouponProduct extends \XLite\Model\Repo\ARepo
{
    use ExecuteCachedTrait;

    const P_COUPON_ID = 'coupon_id';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndCouponId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $value = intval($value);

        $alias = $this->getMainAlias($queryBuilder);
        $queryBuilder->andWhere($alias . '.coupon = :couponId')
            ->setParameter('couponId', $value);
    }

    public function getCouponProductIds($couponId)
    {
        return $this->executeCachedRuntime(function() use ($couponId) {
            $qb = $this->createPureQueryBuilder('cp');

            $qb->select('p.product_id')
                ->linkInner('cp.product', 'p')
                ->andWhere('cp.coupon = :couponId')
                ->setParameter('couponId', $couponId);

            $result = $qb->getResult();

            return array_column($result, 'product_id');
        }, ['getCouponProductIds', $couponId]);
    }
}
