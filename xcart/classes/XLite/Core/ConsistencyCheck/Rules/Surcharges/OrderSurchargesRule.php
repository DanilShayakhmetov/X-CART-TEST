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
use XLite\Model\Order\Surcharge;
use XLite\Model\QueryBuilder\AQueryBuilder;
use \XLite\Model\Repo\Order;

/**
 * Class SurchargesRule
 * @package XLite\Core\ConsistencyCheck\Rules\Surcharges
 */
class OrderSurchargesRule implements RuleInterface
{
    use DefaultModelStringifier;

    /**
     * @var Order\Surcharge
     */
    private $repo;

    /**
     * SurchargesRule constructor.
     *
     * @param Order\Surcharge $repo
     */
    public function __construct(Order\Surcharge $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @return Inconsistency|boolean
     */
    public function execute()
    {
        $invalid = $this->getSurchargesWithoutOrders();

        if ($invalid) {
            $message = \XLite\Core\Translation::getInstance()->translate(
                'There are %model% without valid %another_model% relation',
                [
                    'model'         => 'order surcharges (XLite\Model\Order\Surcharge)',
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
    protected function getSurchargesWithoutOrders()
    {
        /** @var AQueryBuilder $qb */
        $qb = $this->repo->createPureQueryBuilder('s');

        $qb->andWhere('s.owner IS NULL');

        return $qb->getResult();
    }
}
