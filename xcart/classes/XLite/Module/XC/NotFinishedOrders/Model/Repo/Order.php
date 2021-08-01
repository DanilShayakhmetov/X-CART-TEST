<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NotFinishedOrders\Model\Repo;

/**
 * Class represents an order
 */
class Order extends \XLite\Model\Repo\Order implements \XLite\Base\IDecorator
{
    /**
     * Create a new QueryBuilder instance that is prepopulated for this entity name
     *
     * @param string  $alias      Table alias OPTIONAL
     * @param string  $indexBy    The index for the from. OPTIONAL
     * @param boolean $placedOnly Use only orders or orders + carts OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function createQueryBuilder($alias = null, $indexBy = null, $placedOnly = true)
    {
        $result = parent::createQueryBuilder($alias, $indexBy, $placedOnly);

        if ($placedOnly && \XLite::isAdminZone()) {
            $result = $this->addNotFinishedCnd($result);
        }

        return $result;
    }

    /**
     * Add not finished condition to query builder
     *
     * @param \Doctrine\ORM\QueryBuilder  $qb      Query Builder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function addNotFinishedCnd($qb)
    {
        $qb->orWhere($this->defineNotFinishedCndSubquery($qb));

        return $qb;
    }

    /**
     * Define subquery for addNotFinishedCnd() method
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder
     *
     * @return \Doctrine\ORM\Query\Expr\Andx
     */
    protected function defineNotFinishedCndSubquery($qb)
    {
        $qb->linkInner('o.shippingStatus', 'shipping');

        $result = $qb->expr()->andX(
            'o INSTANCE OF XLite\Model\Cart',
            'shipping.code = :shipstatus'
        );

        $qb->setParameter('shipstatus', \XLite\Model\Order\Status\Shipping::STATUS_NOT_FINISHED);

        return $result;
    }

    /**
     * Count items for export routine
     *
     * @return integer
     */
    public function countNFOForExport()
    {
        $countWithNfo = (int) $this->defineCountForExportWithNFOQuery()
                          ->getSingleScalarResult();

        return $countWithNfo - $this->countForExport();
    }

    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForExportWithNFOQuery()
    {
        $qb = $this->defineCountForExportQuery();

        $qb = $this->addNotFinishedCnd($qb);

        return $qb;
    }

    /**
     * Update recent orders condition: exclude not finisged orders
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndRecent(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        parent:: prepareCndRecent($queryBuilder, $value);

        if ($value) {
            $queryBuilder->linkInner('o.shippingStatus', 'shipping');
            $queryBuilder->andWhere(
                $queryBuilder->expr()->not(
                    $this->defineNotFinishedCndSubquery($queryBuilder)
                )
            );
        }
    }
}
