<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Model\Repo;

/**
 * Order review keys repository
 */
class OrderReviewKey extends \XLite\Model\Repo\ARepo
{
    /**
     * Find all review keys available for sending
     *
     * @param integer $limit Limit of results OPTIONAL
     *
     * @return array
     */
    public function findValidReviewKeys($limit = null)
    {
        $queryBuilder = $this->defineFindValidReviewKeys($limit);

        return $queryBuilder->getResult();
    }

    /**
     * Returns query builder
     *
     * @param integer $limit Limit of results OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindValidReviewKeys($limit = null)
    {
        $delay = \XLite\Core\Converter::time() - (\XLite\Core\Config::getInstance()->XC->Reviews->followupTimeout * 86400);

        $qb = $this->createQueryBuilder('ork');

        $qb->andWhere('ork.addedDate < :delay')
            ->andWhere('ork.sentDate = 0')
            ->andWhere($qb->expr()->not($qb->expr()->isNull('ork.order'))) // Paranoid check
            ->setParameter('delay', $delay)
            ->addOrderBy('ork.addedDate');

        if ($limit) {
            $qb->setFrameResults(0, $limit);
        }

        return $qb;
    }
}
