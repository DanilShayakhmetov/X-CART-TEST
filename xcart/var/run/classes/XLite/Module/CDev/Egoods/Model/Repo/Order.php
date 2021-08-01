<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Model\Repo;

/**
 * Order  repository
 */
abstract class Order extends \XLite\Module\CDev\PINCodes\Model\Repo\Order implements \XLite\Base\IDecorator
{
    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndRecent(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        parent::prepareCndRecent($queryBuilder, $value);

        if ($value) {
            $alias = 'EgoodsShippingStatusAlias';

            $queryBuilder->innerJoin('o.shippingStatus', $alias);
            $this->assignRecentCondition($queryBuilder, $alias);
            $queryBuilder->setParameter('shippingStatus', \XLite\Model\Order\Status\Shipping::STATUS_WAITING_FOR_APPROVE);
        }
    }

    /**
     * Assign recent search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     * @param string $alias
     */
    protected function assignRecentCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias)
    {
        $queryBuilder->orWhere($queryBuilder->expr()->andX(
            $alias . '.code = :shippingStatus',
            'o.orderNumber IS NOT NULL'
        ));
    }

    /**
     * Find all orders bu profile WithEgoods
     *
     * @param \XLite\Model\Profile $profile NOT OPTIONAL (default value is deprecated)
     *
     * @param bool                 $availableOnly
     *
     * @return array
     */
    public function findAllOrdersWithEgoods(\XLite\Model\Profile $profile = null, $availableOnly = true)
    {
        $list = [];

        if ($profile) {
            foreach ($this->defineFindAllOrdersWithEgoodsQuery($profile)->getResult() as $order) {
                if ($order->getDownloadAttachments($availableOnly)) {
                    $list[] = $order;
                }
            }
        }

        return $list;
    }

    /**
     * Define query for findAllOrdersWithEgoods() method
     *
     * @param \XLite\Model\Profile $profile Profile OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindAllOrdersWithEgoodsQuery(\XLite\Model\Profile $profile = null)
    {
        $qb = $this->createQueryBuilder('o')
            ->innerJoin('o.items', 'item')
            ->innerJoin('item.privateAttachments', 'pa')
            ->leftJoin('o.orig_profile', 'op')
            ->orderBy('o.date', 'desc');

        if ($profile) {
            $qb->andWhere('op.profile_id = :opid')
                ->setParameter('opid', $profile->getProfileId());
        }

        return $qb;

    }

    /**
     * Search orders by profile WithEgoods
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Count only OPTIONAL
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function searchOrdersWithEgoods(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $queryBuilder = $this->createQueryBuilder('o');

        if ($cnd->limit) {
            $queryBuilder->setFirstResult($cnd->limit[0]);
            $queryBuilder->setMaxResults($cnd->limit[1]);
        }

        if ($cnd->user) {
            $queryBuilder->andWhere('o.orig_profile = :origProfile')
                ->setParameter('origProfile', $cnd->user);
        }

        $queryBuilder->join('o.items', 'items')
            ->innerJoin('items.privateAttachments', 'pa')
            ->leftJoin('o.orig_profile', 'op')
            ->groupBy('o.order_id');

        return $countOnly ? count($queryBuilder->getResult()) : $queryBuilder->getResult();
    }
}

