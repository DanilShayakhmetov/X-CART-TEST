<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\Model\Repo;

/**
 * The "product" model repository
 */
 class Category extends \XLite\Module\XC\RESTAPI\Model\Repo\Category implements \XLite\Base\IDecorator
{
    public function getDemoEntitiesCount()
    {
        $nonEmpty = $this->getNonEmptyCategories();
        $qb = $this->createPureQueryBuilder('c')
            ->select('COUNT(c)')
            ->andWhere('c.demo = 1');

        if (count($nonEmpty) > 0) {
            $qb->andWhere('c.category_id NOT IN (:nonEmpty)')
                ->setParameter('nonEmpty', $nonEmpty);
        }

        $this->addExcludeRootCondition($qb);

        return $qb->getSingleScalarResult();
    }

    public function getNonEmptyCategories()
    {
        $qb = $this->createPureQueryBuilder('c');

        $qb->select('c.category_id')
            ->innerJoin(
                'XLite\Model\Category',
                'c2',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                "c2.lpos >= c.lpos AND c2.rpos <= c.rpos"
            )
            ->innerJoin('c2.categoryProducts', 'cp')
            ->groupBy('c.category_id');

        $result = $qb->getArrayResult();

        return array_map(function($item) {
            return $item['category_id'];
        }, $result);
    }

    public function deleteDemoEntities()
    {
        $nonEmpty = $this->getNonEmptyCategories();
        $qb = $this->createPureQueryBuilder('c')
            ->delete($this->_entityName, 'c')
            ->andWhere('c.demo = 1');

        if (count($nonEmpty) > 0) {
            $qb->andWhere('c.category_id NOT IN (:nonEmpty)')
                ->setParameter('nonEmpty', $nonEmpty);
        }

        return $qb->getQuery()->execute();
    }
}
