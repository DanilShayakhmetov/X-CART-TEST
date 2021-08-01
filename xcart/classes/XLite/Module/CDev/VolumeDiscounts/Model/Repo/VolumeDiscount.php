<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\Model\Repo;

/**
 * @Api\Operation\Create(modelClass="XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount", summary="Add volume discount")
 * @Api\Operation\Read(modelClass="XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount", summary="Retrieve volume discount by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount", summary="Retrieve volume discounts by conditions")
 * @Api\Operation\Update(modelClass="XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount", summary="Update volume discount by id")
 * @Api\Operation\Delete(modelClass="XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount", summary="Delete volume discount by id")
 *
 * @SWG\Tag(
 *   name="CDev\VolumeDiscounts\VolumeDiscount",
 *   x={"display-name": "VolumeDiscount", "group": "CDev\VolumeDiscounts"},
 *   description="Volume discount record keeps data about discount tiers",
 *   @SWG\ExternalDocumentation(
 *     description="Find out more about setting up volume discounts",
 *     url="https://kb.x-cart.com/en/seo_and_promotion/setting_up_volume_discounts_for_products.html"
 *   )
 * )
 */
class VolumeDiscount extends \XLite\Model\Repo\ARepo
{
    /**
     * Allowable search params
     */
    const P_MEMBERSHIP = 'membership';
    const P_SUBTOTAL = 'subtotal';
    const P_SUBTOTAL_ADV = 'subtotalAdv';
    const P_MIN_VALUE = 'minValue';
    const P_ZONES = 'zones';
    const P_TYPE = 'type';
    const P_DATE = 'date';
    const P_ORDER_BY_VALUE = 'orderByValue';
    const P_ORDER_BY_SUBTOTAL = 'orderBySubtotal';
    const P_ORDER_BY_SUBTOTAL_AND_VALUE = 'orderBySubtotalAndValue';
    const P_ORDER_BY_MEMBERSHIP = 'orderByMembership';

    /**
     * Find similar discounts
     *
     * @param \XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount $model Discount
     *
     * @return \XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount
     */
    public function findSimilarDiscounts(\XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount $model)
    {
        return $this->defineFindSimilarDiscountsQuery($model)->getResult();
    }

    /**
     * Define query for 'findSimilarDiscounts' method
     *
     * @param \XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount $model Discount
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindSimilarDiscountsQuery(\XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount $model)
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('v.subtotalRangeBegin = :subtotalRangeBegin')
            ->andWhere('v.dateRangeBegin = :dateRangeBegin')
            ->andWhere('v.dateRangeEnd = :dateRangeEnd')
            ->setParameter('subtotalRangeBegin', $model->getSubtotalRangeBegin())
            ->setParameter('dateRangeBegin', $model->getDateRangeBegin())
            ->setParameter('dateRangeEnd', $model->getDateRangeEnd());

        if ($model->getMembership()) {
            $qb->andWhere('v.membership = :membership')
                ->setParameter('membership', $model->getMembership());
        } else {
            $qb->andWhere('v.membership IS NULL');
        }

        if ($model->getId()) {
            $qb->andWhere('v.id <> :id')
                ->setParameter('id', $model->getId());
        }

        return $qb;
    }

    /**
     * Prepare conditions for search
     *
     * @return void
     */
    protected function processConditions()
    {
        $membershipRelation = false;

        foreach ($this->searchState['currentSearchCnd'] as $key => $value) {
            if (in_array($key, array(self::P_MEMBERSHIP, self::P_ORDER_BY_MEMBERSHIP), true)) {
                $membershipRelation = true;
            }
        }

        if ($membershipRelation) {
            $this->searchState['queryBuilder']->leftJoin('v.membership', 'membership');
        }

        parent::processConditions();
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function prepareCndMembership(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (null !== $value) {
            $cnd = new \Doctrine\ORM\Query\Expr\Orx();
            $cnd->add('membership.membership_id = :membershipId');
            $cnd->add('v.membership IS NULL');

            $queryBuilder->andWhere($cnd)
                ->setParameter('membershipId', $value);

        } else {
            $queryBuilder->andWhere('v.membership IS NULL');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function prepareCndSubtotal(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder->andWhere('v.subtotalRangeBegin <= :subtotal')
            ->setParameter('subtotal', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndSubtotalAdv(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder->andWhere('v.subtotalRangeBegin > :subtotal')
            ->setParameter('subtotal', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndMinValue(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder->andWhere('v.value > :value')
            ->setParameter('value', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function prepareCndType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder->andWhere('v.type = :type')
            ->setParameter('type', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function prepareCndZones(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder->linkLeft('v.zones', 'zone');

        if (null !== $value) {
            $zoneIds = array_map(function ($zone) {
                return $zone->getZoneId();
            }, $value);

            $cnd = $queryBuilder->expr()->orX();
            $cnd->add($queryBuilder->expr()->in('zone.zone_id', ':zoneIds'));
            $cnd->add('zone.zone_id IS NULL');

            $queryBuilder->andWhere($cnd);
            $queryBuilder->setParameter('zoneIds', $zoneIds);

        } else {
            $queryBuilder->andWhere('zone.zone_id IS NULL');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    protected function prepareCndDate(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $dateRangeEndCnd = $queryBuilder->expr()->orX();
        $dateRangeEndCnd->add('v.dateRangeEnd >= :date');
        $dateRangeEndCnd->add('v.dateRangeEnd = 0');

        $cnd = $queryBuilder->expr()->andX();
        $cnd->add('v.dateRangeBegin <= :date');
        $cnd->add($dateRangeEndCnd);

        $queryBuilder->andWhere($cnd);
        $queryBuilder->setParameter('date', $value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndOrderByValue(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value, $countOnly)
    {
        $this->prepareCndOrderBy($queryBuilder, $value, $countOnly);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndOrderBySubtotalAndValue(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value, $countOnly)
    {
        $this->prepareCndOrderBy($queryBuilder, $value, $countOnly);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndOrderBySubtotal(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value, $countOnly)
    {
        $this->prepareCndOrderBy($queryBuilder, $value, $countOnly);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndOrderByMembership(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value, $countOnly)
    {
        $this->prepareCndOrderBy($queryBuilder, $value, $countOnly);
    }

    // }}}

    // {{{ Find suitable discount methods

    /**
     * Get suitable discount with max value for specified subtotal
     *
     * @param \XLite\Core\CommonCell $cnd Condition
     *
     * @return \XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount
     */
    public function getSuitableMaxDiscount($cnd)
    {
        // Get suitable percent and absolute discounts ordered by value,
        // so max value discount will be the first element
        $percentDiscounts = $this->search($this->getSuitablePercentDiscountsCondition($cnd));
        $absoluteDiscounts = $this->search($this->getSuitableAbsoluteDiscountsCondition($cnd));

        if ($percentDiscounts && $absoluteDiscounts) {
            $maxDiscount = $percentDiscounts[0]->getValue() * $cnd->{self::P_SUBTOTAL} / 100 > $absoluteDiscounts[0]->getValue()
                ? $percentDiscounts[0]
                : $absoluteDiscounts[0];
        } elseif ($percentDiscounts) {
            $maxDiscount = $percentDiscounts[0];
        } elseif ($absoluteDiscounts) {
            $maxDiscount = $absoluteDiscounts[0];
        } else {
            $maxDiscount = null;
        }

        return $maxDiscount;
    }

    /**
     * Get next discount
     *
     * @param \XLite\Core\CommonCell $cnd Condition
     *
     * @return \XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount
     */
    public function getNextDiscount($cnd)
    {
        // Get suitable percent and absolute discounts ordered by subtotal asc and value desc,
        // so max value discount will be the first element
        $percentDiscounts = $this->search($this->getSuitablePercentDiscountsCondition($cnd, true));
        $absoluteDiscounts = $this->search($this->getSuitableAbsoluteDiscountsCondition($cnd, true));

        if ($percentDiscounts && $absoluteDiscounts) {
            if ($percentDiscounts[0]->getSubtotalRangeBegin() < $absoluteDiscounts[0]->getSubtotalRangeBegin()) {
                $maxDiscount = $percentDiscounts[0];
            } elseif ($percentDiscounts[0]->getSubtotalRangeBegin() > $absoluteDiscounts[0]->getSubtotalRangeBegin()) {
                $maxDiscount = $absoluteDiscounts[0];
            } else {
                $maxDiscount = $percentDiscounts[0]->getValue() * $cnd->{self::P_SUBTOTAL} / 100 > $absoluteDiscounts[0]->getValue()
                    ? $percentDiscounts[0]
                    : $absoluteDiscounts[0];
            }
        } elseif ($percentDiscounts) {
            $maxDiscount = $percentDiscounts[0];
        } elseif ($absoluteDiscounts) {
            $maxDiscount = $absoluteDiscounts[0];
        } else {
            $maxDiscount = null;
        }

        return $maxDiscount;
    }

    /**
     * getSuitableDiscountsCondition
     *
     * @param \XLite\Core\CommonCell $cnd Condition
     * @param bool $isNext
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSuitableDiscountsCondition($cnd, $isNext = false)
    {
        $result = new \XLite\Core\CommonCell();

        if ($isNext) {
            $result->{self::P_SUBTOTAL_ADV} = $cnd->{self::P_SUBTOTAL};
            $result->{self::P_ORDER_BY_SUBTOTAL_AND_VALUE} = [['v.subtotalRangeBegin', 'ASC'], ['v.value', 'DESC']];
        } else {
            $result->{self::P_SUBTOTAL} = $cnd->{self::P_SUBTOTAL};
            $result->{self::P_ORDER_BY_VALUE} = ['v.value', 'DESC'];
        }

        $membership = $cnd->{self::P_MEMBERSHIP};
        $result->{self::P_MEMBERSHIP} = $membership ? $membership->getMembershipId() : null;

        $result->{self::P_ZONES} = $cnd->{self::P_ZONES};

        $result->{self::P_DATE} = \XLite\Core\Converter::time();

        return $result;
    }

    /**
     * getSuitablePercentDiscountsCondition
     *
     * @param \XLite\Core\CommonCell $cnd Condition
     * @param bool $isNext
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSuitablePercentDiscountsCondition($cnd, $isNext = false)
    {
        $result = $this->getSuitableDiscountsCondition($cnd, $isNext);
        $result->{self::P_TYPE} = \XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount::TYPE_PERCENT;

        return $result;
    }

    /**
     * getSuitableAbsoluteDiscountsCondition
     *
     * @param \XLite\Core\CommonCell $cnd Condition
     * @param bool $isNext
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSuitableAbsoluteDiscountsCondition($cnd, $isNext = false)
    {
        $result = $this->getSuitableDiscountsCondition($cnd, $isNext);
        $result->{self::P_TYPE} = \XLite\Module\CDev\VolumeDiscounts\Model\VolumeDiscount::TYPE_ABSOLUTE;

        return $result;
    }

    // }}}
}
