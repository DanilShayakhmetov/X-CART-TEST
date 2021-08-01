<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Model\Repo;


use Doctrine\ORM\NoResultException;

 class Profile extends \XLite\Model\Repo\ProfileAbstract implements \XLite\Base\IDecorator
{
    /**
     * @return \XLite\Model\Profile|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findDumpProfile()
    {
        $qb = $this->createPureQueryBuilder('p');
        $expr = $qb->expr();
        $qb->where($qb->expr()->andX(
            $expr->isNull('p.order'),
            $expr->gte('p.access_level', \XLite\Core\Auth::getInstance()->getAdminAccessLevel())
        ))
            ->setMaxResults(1)
            ->orderBy('p.added');


        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            $qb->where($qb->expr()->andX(
                $expr->isNull('p.order')
            ));

            try {
                return $qb->getQuery()->getSingleResult();
            } catch (NoResultException $e) {
                return null;
            }
        }
    }
}