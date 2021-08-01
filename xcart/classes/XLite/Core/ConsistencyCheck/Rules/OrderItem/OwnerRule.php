<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ConsistencyCheck\Rules\OrderItem;

use XLite\Core\ConsistencyCheck\DefaultModelStringifier;
use XLite\Core\ConsistencyCheck\Inconsistency;
use XLite\Core\ConsistencyCheck\InconsistencyEntities;
use XLite\Core\ConsistencyCheck\RuleInterface;
use XLite\Model\QueryBuilder\AQueryBuilder;
use XLite\Model\Repo;

class OwnerRule implements RuleInterface
{
    use DefaultModelStringifier;

    /**
     * @var Repo\OrderItem
     */
    private $repo;

    /**
     * SurchargesRule constructor.
     *
     * @param Repo\OrderItem $repo
     */
    public function __construct(Repo\OrderItem $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @return Inconsistency|bool
     */
    public function execute()
    {
        $invalid = $this->getOrderItemsWithoutOrders();

        if ($invalid) {
            $message = \XLite\Core\Translation::getInstance()->translate(
                'There are %model% without valid %another_model% relation',
                [
                    'model'         => 'orderItems (XLite\Model\OrderItem)',
                    'another_model' => 'order (XLite\Model\Order)',
                ]
            );
            return new InconsistencyEntities(
                Inconsistency::ERROR,
                $message,
                array_map(function($v) {
                    return $this->stringifyModel($v);
                }, $invalid)
            );
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getOrderItemsWithoutOrders()
    {
        /** @var AQueryBuilder $qb */
        $qb = $this->repo->createPureQueryBuilder('oi');

        $qb->andWhere('oi.order IS NULL');

        return $qb->getResult();
    }
}
