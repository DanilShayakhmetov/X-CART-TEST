<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Model\Repo;

/**
 * The "product" model repository
 */
class Product extends \XLite\Model\Repo\Product implements \XLite\Base\IDecorator
{
    /**
     * Count items for FacebookProductFeed routine
     *
     * @return integer
     */
    public function countForFacebookProductFeed()
    {
        return (int)$this->defineCountForFacebookProductFeedQuery()->getSingleScalarResult();
    }

    /**
     * Define items iterator
     *
     * @param integer $position Position OPTIONAL
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getFacebookProductFeedIterator($position = 0)
    {
        return $this->defineFacebookProductFeedIteratorQueryBuilder($position)->iterate();
    }

    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForFacebookProductFeedQuery()
    {
        $qb = $this->createPureQueryBuilder();

        $this->assignEnabledCondition($qb);

        $qb->select('COUNT(DISTINCT ' . $this->getDefaultAlias() . '.' . $this->getPrimaryKeyField() . ')')
            ->andWhere($this->getMainAlias($qb) . '.facebookMarketingEnabled = :fbMarketingFlag')
            ->setParameter('fbMarketingFlag', true);

        return $qb;
    }

    /**
     * Define FacebookProductFeed iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFacebookProductFeedIteratorQueryBuilder($position)
    {
        $qb = $this->createPureQueryBuilder();

        $this->assignEnabledCondition($qb);

        $qb->andWhere($this->getMainAlias($qb) . '.facebookMarketingEnabled = :fbMarketingFlag')
            ->setParameter('fbMarketingFlag', true)
            ->setFirstResult($position)
            ->setMaxResults(\XLite\Module\XC\FacebookMarketing\Core\EventListener\ProductFeedGeneration::CHUNK_LENGTH)
            ->orderBy($this->getMainAlias($qb) . '.product_id');

        return $qb;
    }
}