<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ConsistencyCheck\Rules\Order;

use XLite\Core\ConsistencyCheck\DefaultModelStringifier;
use XLite\Core\ConsistencyCheck\Inconsistency;
use XLite\Core\ConsistencyCheck\InconsistencyEntities;
use XLite\Core\ConsistencyCheck\RuleInterface;
use XLite\Model\QueryBuilder\AQueryBuilder;
use XLite\Model\Repo;

class PaymentStatusExistsRule implements RuleInterface
{
    use OrderModelStringifier;

    /**
     * @var Repo\Order
     */
    private $repo;

    /**
     * SurchargesRule constructor.
     *
     * @param Repo\Order $repo
     */
    public function __construct(Repo\Order $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @return Inconsistency|bool
     */
    public function execute()
    {
        $invalid = $this->getOrdersWithoutPaymentStatus();

        if ($invalid) {
            $message = \XLite\Core\Translation::getInstance()->translate(
                'There are %model% with missing %another_model%',
                [
                    'model'         => 'orders (XLite\Model\Order)',
                    'another_model' => 'paymentStatus (XLite\Model\Order\Status\Payment)',
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
    protected function getOrdersWithoutPaymentStatus()
    {
        /** @var AQueryBuilder $qb */
        $qb = $this->repo->createPureQueryBuilder('o');

        $qb->andWhere('o.paymentStatus IS NULL');

        return $qb->getResult();
    }
}
