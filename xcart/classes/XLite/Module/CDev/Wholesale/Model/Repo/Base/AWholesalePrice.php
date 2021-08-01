<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Model\Repo\Base;
use XLite\Module\CDev\Wholesale\Model\Base\AWholesalePrice as AWholesalePriceModel;

/**
 * WholesalePrice model repository
 */
class AWholesalePrice extends \XLite\Model\Repo\ARepo
{
    /**
     * Allowable search params
     */
    const P_MEMBERSHIP          = 'membership';
    const P_QTY                 = 'quantity';
    const P_MIN_QTY             = 'minQuantity';
    const P_ORDER_BY_MEMBERSHIP = 'orderByMembership';

    /**
     * Get default alias
     *
     * @return string
     */
    public function getDefaultAlias()
    {
        return 'w';
    }

    // {{{ Additional helper methods

    /**
     * Re-calculate quantityRangeEnd value for each price
     *
     * @param mixed $object Object
     *
     * @return void
     */
    public function correctQuantityRangeEnd($object)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{self::P_ORDER_BY} = ['w.quantityRangeBegin', 'ASC'];

        // Get all prices
        $allPrices = $this->search($this->processContition($cnd, $object));

        if ($allPrices) {

            // Calculate new quantityRangeEnd values for all prices...

            $membershipsHash = [];
            $maxQuantities = [];

            // Get hash of quantityRangeBegin for all prices (group by membership)
            foreach ($allPrices as $key => $price) {
                $membershipId = $price->getMembership() ? $price->getMembership()->getMembershipId() : 0;
                $membershipsHash[$membershipId][$key] = $price->getQuantityRangeBegin();
            }

            // Max allowed value for quantityRangeEnd
            $absMaxQuantity = pow(10, 16);

            // Find quantityRangeEnd for each price and store it in array $maxQuantities
            foreach ($membershipsHash as $membershipId => $membershipDiscounts) {

                foreach ($membershipDiscounts as $priceKey => $minQuantity) {

                    $maxQuantity = $absMaxQuantity;

                    foreach ($membershipDiscounts as $quantity) {
                        if ($quantity > $minQuantity && $quantity < $maxQuantity) {
                            $maxQuantity = $quantity - 1;
                        }
                    }

                    if ($maxQuantity == $absMaxQuantity) {
                        $maxQuantity = 0;
                    }

                    $maxQuantities[$priceKey] = $maxQuantity;
                }
            }

            $needUpdate = false;

            // Update quantityRangeEnd value if it differs from current value
            foreach ($allPrices as $key => $price) {
                if ($price->getQuantityRangeEnd() != $maxQuantities[$key]) {
                    $price->setQuantityRangeEnd($maxQuantities[$key]);
                    \XLite\Core\Database::getEM()->persist($price);
                    $needUpdate = true;
                }
            }

            if ($needUpdate) {
                \XLite\Core\Database::getEM()->flush();
            }
        } // if ($allPrices)
    }

    /**
     * Check if the object has any wholesale price
     *
     * @param mixed $object Object
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function hasWholesalePrice($object)
    {
        $cnd = new \XLite\Core\CommonCell();

        return 0 < count($this->search($this->processContition($cnd, $object)));
    }

    /**
     * Return price under amount and membership conditions
     *
     * @param mixed                   $object     Object
     * @param integer                 $amount     Quantity of product
     * @param \XLite\Model\Membership $membership Membership object OPTIONAL
     *
     * @return float Product variant price
     * @return null  Null price means the default value for specific price type must be used
     */
    public function getPrice($object, $amount, $membership = null)
    {

        if (
            1 == $amount
            && !$membership
        ) {
            $minPrice = $object->getBasePrice();

        } else {
            $cnd = new \XLite\Core\CommonCell();

            $cnd->{static::P_MEMBERSHIP}          = $membership;
            $cnd->{static::P_QTY}                 = $amount;
            $cnd->{static::P_ORDER_BY}            = ['w.price', 'ASC'];
            $cnd->{static::P_ORDER_BY_MEMBERSHIP} = false;

            $prices = $this->search($this->processContition($cnd, $object));

            $minPrice = null;
            foreach ($prices as $entity) {
                if ($entity->getType() === AWholesalePriceModel::WHOLESALE_TYPE_PERCENT) {
                    $price = $object->getBasePrice() * $entity->getPrice() / 100;
                } else {
                    $price = $entity->getPrice();
                }

                $currencyE = \XLite::getInstance()->getCurrency()->getE();

                $price = round($price, $currencyE);

                if (is_null($minPrice) || $price < $minPrice) {
                    $minPrice = $price;
                }
            }
        }

        return $minPrice;
    }

    /**
     * Return wholesale prices for the given product
     *
     * @param mixed                   $object     Object
     * @param \XLite\Model\Membership $membership Membership object OPTIONAL
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getWholesalePrices($object, $membership = null)
    {
        $cnd = new \XLite\Core\CommonCell();

        $minQty = $object->getMinQuantity($membership);

        $cnd->{static::P_MEMBERSHIP} = $membership;
        $cnd->{static::P_MIN_QTY}    = $minQty;
        $cnd->{static::P_ORDER_BY}   = ['w.quantityRangeBegin', 'ASC'];
        $cnd->{static::P_ORDER_BY_MEMBERSHIP} = false;

        $prices = $this->search($this->processContition($cnd, $object));

        if (empty($prices)) {
            return [];
        }

        if (1 < $minQty) {
            foreach ($prices as $key => $price) {
                if ($prices[$key]->getQuantityRangeBegin() < $minQty) {
                    $prices[$key]->setQuantityRangeBegin($minQty);
                }
            }
        }

        if (!empty($membership)) {

            $result = [];

            foreach ($prices as $key => $price) {
                if (!empty($minimalPrice)
                    && !empty($lastKey)
                    && $result[$lastKey]->getQuantityRangeBegin() < $price->getQuantityRangeBegin()) {
                    $result[$lastKey]->setQuantityRangeEnd($price->getQuantityRangeBegin() - 1);
                }

                //get all ranges for quantity point
                $rangesHaving = [];
                foreach ($prices as $rangeKey => $range) {
                    if ($price->getQuantityRangeBegin() >= $range->getQuantityRangeBegin()
                        && ($range->getQuantityRangeEnd() == 0
                            || $price->getQuantityRangeBegin() <= $range->getQuantityRangeEnd())
                        ) {
                        $rangesHaving[] = $range;
                    }
                }

                $minimalPrice = null;
                $minimalPriceValue = null;
                if (!empty($rangesHaving)) {
                    //get minimal price range for quantity point
                    foreach ($rangesHaving as $rangeKey => $range) {
                        if ($range->getType() === AWholesalePriceModel::WHOLESALE_TYPE_PERCENT) {
                            $rangePriceValue = $object->getBasePrice() * $range->getPrice() / 100;
                        } else {
                            $rangePriceValue = $range->getPrice();
                        }

                        if (empty($minimalPrice) || $rangePriceValue < $minimalPriceValue) {
                            $minimalPrice = $range;
                            $minimalPriceValue = $rangePriceValue;
                        }
                    }
                    $result[] = clone $minimalPrice;
                    end($result);
                    $lastKey = key($result);
                    reset($result);
                }
            }

            $prices = $result;
        }

        // Transform qty ranges with same price to the single range
        if (!empty($prices)) {
            $currentKey = null;

            foreach ($prices as $key => $price) {
                if (!isset($currentKey)) {
                    $currentKey = $key;

                    continue;
                }

                if (
                    $prices[$currentKey]->getPrice() == $price->getPrice()
                    && $prices[$currentKey]->getType() === $price->getType()
                ) {
                    $prices[$currentKey]->setQuantityRangeEnd($price->getQuantityRangeEnd());
                    unset($prices[$key]);
                } else {
                    $currentKey = $key;
                }
            }
        }

        if (
            1 == count($prices)
            && isset($prices[0])
            && $minQty >= $prices[0]->getQuantityRangeBegin()
        ) {
            $prices = [];
        }

        return $prices;
    }

    // }}}

    /**
     * Excluded search conditions
     *
     * @return array
     */
    protected function getExcludedConditions()
    {
        return array_merge(
            parent::getExcludedConditions(),
            [
                static::P_ORDER_BY_MEMBERSHIP  => static::EXCLUDE_FROM_ANY,
            ]
        );
    }

    /**
     * Prepare conditions for search
     *
     * @return void
     */
    protected function processConditions()
    {
        $cnd = $this->searchState['currentSearchCnd'];

        $membershipRelation = false;
        foreach ($cnd as $key => $value) {
            if (in_array($key, [self::P_MEMBERSHIP, self::P_ORDER_BY_MEMBERSHIP], true)) {
                $membershipRelation = true;
            }
        }

        if ($membershipRelation) {
            $this->searchState['queryBuilder']->leftJoin('w.membership', 'membership');
        }

        $expr = new \Doctrine\ORM\Query\Expr\Orx();
        $expr->add('w.quantityRangeBegin <> 1');
        $expr->add('w.membership IS NOT NULL');
        $this->searchState['queryBuilder']->andWhere($expr);

        if ($cnd->{self::P_ORDER_BY_MEMBERSHIP}) {
            $cnd->{static::P_ORDER_BY} = $this->getOrderByWithMembership(
                $cnd->{self::P_ORDER_BY_MEMBERSHIP},
                $cnd->{static::P_ORDER_BY}
            );
        }
        
        parent::processConditions();
    }

    /**
     * @param $orderByMembership
     * @param $currentOrderBy
     *
     * @return array
     */
    protected function getOrderByWithMembership($orderByMembership, $currentOrderBy)
    {
        $cndToAdd = [ 'membership.membership_id', $orderByMembership ? 'ASC' : 'DESC' ];

        if ($currentOrderBy && is_array($currentOrderBy)) {
            $currentOrderBy = is_array($currentOrderBy[0]) ? $currentOrderBy : [$currentOrderBy];
            array_unshift($currentOrderBy, $cndToAdd);
        } else {
            $currentOrderBy = [ $cndToAdd ];
        }

        return $currentOrderBy;
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
    protected function prepareCndMembership(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (!empty($value)) {

            if (is_object($value)) {
                $value = $value->getMembershipId();
            }

            $cnd = new \Doctrine\ORM\Query\Expr\Orx();
            $cnd->add('membership.membership_id = :membershipId');
            $cnd->add('w.membership IS NULL');

            $queryBuilder->andWhere($cnd)
                ->setParameter('membershipId', $value);

        } else {
            $queryBuilder->andWhere('w.membership IS NULL');
        }
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
    protected function prepareCndQuantity(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $cnd = new \Doctrine\ORM\Query\Expr\Orx();
        $cnd->add('w.quantityRangeEnd >= :qty');
        $cnd->add('w.quantityRangeEnd = 0');

        $queryBuilder->andWhere('w.quantityRangeBegin <= :qty')
            ->andWhere($cnd)
            ->setParameter('qty', $value);
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
    protected function prepareCndMinQuantity(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $cnd = new \Doctrine\ORM\Query\Expr\Orx();
        $cnd->add('w.quantityRangeEnd >= :minQty');
        $cnd->add('w.quantityRangeEnd = 0');

        $queryBuilder->andWhere($cnd)
            ->setParameter('minQty', $value);
    }

    // }}}
}
