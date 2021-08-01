<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\Model\Repo;

/**
 * Products repository
 */
abstract class Product extends \XLite\Module\XC\Onboarding\Model\Repo\Product implements \XLite\Base\IDecorator
{
    /**
     * Define sitemap generation iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFeedGenerationQueryBuilder($position)
    {
        $qb = parent::defineFeedGenerationQueryBuilder($position);

        $this->assignEnabledCondition($qb);
        $this->assignGoogleFeedEnabledCondition($qb);

        $alias = $qb->getRootAliases()[0];
        $qb->orderBy($alias . '.product_id');

        return $qb;
    }

    /**
     * Assign google feed enabled condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     * @param string                     $alias        Alias OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function assignGoogleFeedEnabledCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        $alias = $alias ?: $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere($alias . '.googleFeedEnabled = :enabled')
            ->setParameter('enabled', true);

        return $queryBuilder;
    }
}
