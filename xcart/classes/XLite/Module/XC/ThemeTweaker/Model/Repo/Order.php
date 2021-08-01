<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Model\Repo;

/**
 * Order repository
 */
class Order extends \XLite\Model\Repo\Order implements \XLite\Base\IDecorator
{
    /**
     * @return \XLite\Core\CommonCell
     */
    public function defineCndDumpOrder()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->paymentStatus = [
            \XLite\Model\Order\Status\Payment::STATUS_PAID,
            \XLite\Model\Order\Status\Payment::STATUS_QUEUED,
            \XLite\Model\Order\Status\Payment::STATUS_PART_PAID,
        ];
        $cnd->orderBy = ['o.date', 'desc'];
        $cnd->limit = [0, 1];

        return $cnd;
    }

    /**
     * @return null|\XLite\Model\Order
     */
    public function findDumpOrder()
    {
        $cnd = $this->defineCndDumpOrder();

        $result = $this->search($cnd);

        if (count($result) === 0) {
            unset($cnd->paymentStatus);

            $result = $this->search($cnd);
        }

        return count($result) ? $result[0] : null;
    }

    /**
     * @param $term
     * @param $max
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindByTermQB($term, $max)
    {
        $qb = $this->createPureQueryBuilder('o');

        return $qb->andWhere($qb->expr()->like(
            'o.orderNumber',
            ':term'
        ))
            ->setMaxResults((int)$max)
            ->setParameter('term', '%' . addcslashes($term, '%_') . '%');
    }

    /**
     * @param     $term
     * @param int $max
     *
     * @return array
     */
    public function findOrdersByTerm($term, $max = 1)
    {
        return $this->defineFindByTermQB($term, $max)
            ->getQuery()
            ->getResult();
    }
}
