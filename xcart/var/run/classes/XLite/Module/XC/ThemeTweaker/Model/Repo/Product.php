<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Model\Repo;


use Doctrine\ORM\NoResultException;

 class Product extends \XLite\Model\Repo\ProductAbstract implements \XLite\Base\IDecorator
{
    /**
     * @return \XLite\Model\Product|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findDumpProduct()
    {
        $qb = $this->addLanguageQuery($this->createPureQueryBuilder());
        $this->addSortByTranslation($qb, 'translations.name', 'ASC');
        $qb->setMaxResults(1)
            ->orderBy('calculatedName', 'ASC');

        try {
            $result = $qb->getQuery()->getSingleResult();

            return is_array($result) ? reset($result) : $result;
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @return \XLite\Model\Product[]
     */
    public function findDumpProducts()
    {
        $qb = $this->addLanguageQuery($this->createPureQueryBuilder());
        $this->addSortByTranslation($qb, 'translations.name', 'ASC');
        $qb->setMaxResults(2)
            ->orderBy('calculatedName', 'ASC');

        $result = $qb->getQuery()->getResult();

        return array_map('reset', $result);
    }

    /**
     * @param $term
     * @param $max
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindByTermQB($term, $max)
    {
        $qb = $this->createPureQueryBuilder('p');

        return $qb->andWhere($qb->expr()->like(
            'p.sku',
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
    public function findProductsByTerm($term, $max = 1)
    {
        return $this->defineFindByTermQB($term, $max)
            ->getQuery()
            ->getResult();
    }
}