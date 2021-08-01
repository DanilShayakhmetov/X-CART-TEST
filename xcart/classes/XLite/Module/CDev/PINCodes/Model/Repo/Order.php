<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Model\Repo;

/**
 * Orders repository
 *
 */

class Order extends \XLite\Model\Repo\Order implements \XLite\Base\IDecorator
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
            $alias = 'PinCodesShippingStatusAlias';

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
     * Search orders with pincodes
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Count only OPTIONAL
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function searchWithPinCodes(\XLite\Core\CommonCell $cnd, $countOnly = false)
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
            ->linkInner('items.pinCodes')
            ->andWhere('pinCodes.isSold = :true')
            ->setParameter('true', true)
            ->groupBy('o.order_id');

        return $countOnly ? count($queryBuilder->getResult()) : $queryBuilder->getResult();
    }
}
