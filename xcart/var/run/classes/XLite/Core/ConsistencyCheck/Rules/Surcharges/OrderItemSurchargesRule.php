<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ConsistencyCheck\Rules\Surcharges;

use XLite\Core\ConsistencyCheck\DefaultModelStringifier;
use XLite\Core\ConsistencyCheck\Inconsistency;
use XLite\Core\ConsistencyCheck\InconsistencyEntities;
use XLite\Core\ConsistencyCheck\RuleInterface;
use XLite\Model\QueryBuilder\AQueryBuilder;
use \XLite\Model\Repo\OrderItem;

/**
 * Class SurchargesRule
 * @package XLite\Core\ConsistencyCheck\Rules\Surcharges
 */
class OrderItemSurchargesRule implements RuleInterface
{
    use DefaultModelStringifier;

    /**
     * @var OrderItem\Surcharge
     */
    private $repo;

    /**
     * SurchargesRule constructor.
     *
     * @param OrderItem\Surcharge $repo
     */
    public function __construct(OrderItem\Surcharge $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @return Inconsistency|boolean
     */
    public function execute()
    {
        $invalid = $this->getSurchargesWithoutOrderItems();

        if ($invalid) {
            $message = \XLite\Core\Translation::getInstance()->translate(
                'There are %model% without valid %another_model% relation',
                [
                    'model'         => 'orderItems surcharges (XLite\Model\OrderItem\Surcharge)',
                    'another_model' => 'orderItem (XLite\Model\OrderItem)',
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
    protected function getSurchargesWithoutOrderItems()
    {
        /** @var AQueryBuilder $qb */
        $qb = $this->repo->createPureQueryBuilder('s');

        $qb->andWhere('s.owner IS NULL');

        return $qb->getResult();
    }
}
