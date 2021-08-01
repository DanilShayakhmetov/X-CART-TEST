<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Model\Repo;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * SaleDiscountProduct repo
 */
class SaleDiscount extends \XLite\Model\Repo\Base\I18n
{
    use ExecuteCachedTrait;

    public function findAllProductSpecific()
    {
        return $this->executeCachedRuntime(function() {
            $qb = $this->createPureQueryBuilder('sd')
                ->andWhere('sd.specificProducts = :specificProducts')
                ->setParameter('specificProducts', true);

            return $qb->getResult();
        });
    }

    public function findAllActive()
    {
        return $this->executeCachedRuntime(function() {
            $qb = $this->createPureQueryBuilder('sd');
            $qb->andWhere('sd.enabled = :enabled')
                ->andWhere($qb->expr()->orX('sd.dateRangeBegin = 0', 'sd.dateRangeBegin < :time'))
                ->andWhere($qb->expr()->orX('sd.dateRangeEnd = 0', 'sd.dateRangeEnd > :time'))
                ->setParameter('enabled', true)
                ->setParameter('time', time())
                ->addOrderBy('sd.id', 'ASC');

            return $qb->getResult();
        });
    }

    public function findAllActiveForCalculate()
    {
        return $this->executeCachedRuntime(function() {
            $qb = $this->createPureQueryBuilder('sd');
            $qb->andWhere('sd.enabled = :enabled')
                ->andWhere($qb->expr()->orX('sd.dateRangeBegin = 0', 'sd.dateRangeBegin < :time'))
                ->andWhere($qb->expr()->orX('sd.dateRangeEnd = 0', 'sd.dateRangeEnd > :time'))
                ->setParameter('enabled', true)
                ->setParameter('time', time())
                ->addOrderBy('sd.value', 'DESC');

            return $qb->getResult();
        });
    }

    /**
     * Find discount by name (any language)
     *
     * @param string  $name       Name
     * @param boolean $onlyActive Search only in enabled mebmerships OPTIONAL
     *
     * @return \XLite\Module\CDev\Sale\Model\SaleDiscount|void
     */
    public function findOneByName($name, $onlyActive = false)
    {
        return $this->defineOneByNameQuery($name, $onlyActive)->getSingleResult();
    }

    /**
     * Define query builder for findOneByName() method
     *
     * @param string  $name       Name
     * @param boolean $onlyActive Search only in enabled sale discounts
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineOneByNameQuery($name, $onlyActive)
    {
        $qb = $this->addLanguageQuery($this->createPureQueryBuilder());

        if ($this->getTranslationCode() !== \XLite::getDefaultLanguage()) {
            $this->addDefaultTranslationJoins(
                $qb,
                $this->getMainAlias($qb),
                'defaults',
                \XLite::getDefaultLanguage()
            );
            $qb->andWhere('(CASE WHEN translations.name IS NOT NULL
                            THEN translations.name
                            ELSE default.name END) = :name');

        } else {
            $qb->andWhere('translations.name = :name');
        }

        $qb->setParameter('name', $name)
            ->setMaxResults(1);

        if ($onlyActive) {
            $qb->andWhere('m.enabled = :true');
            $qb->setParameter('true', true);
        }

        return $qb;
    }
}
