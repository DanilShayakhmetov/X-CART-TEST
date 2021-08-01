<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Order tracking number repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\OrderTrackingNumber", summary="Add new order tracking number")
 * @Api\Operation\Read(modelClass="XLite\Model\OrderTrackingNumber", summary="Retrieve order tracking number by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\OrderTrackingNumber", summary="Retrieve order tracking numbers by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\OrderTrackingNumber", summary="Update order tracking number by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\OrderTrackingNumber", summary="Delete order tracking number by id")
 */
class OrderTrackingNumber extends \XLite\Model\Repo\ARepo
{
    /**
     * Search parameter names
     */
    const P_ORDER_ID = 'orderId';

    /**
     * Get default alias
     *
     * @return string
     */
    public function getDefaultAlias()
    {
        return 'tr';
    }

    /**
     * @Api\Condition(description="Filters tracking numbers by order id", type="integer")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrderId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            $queryBuilder->andWhere('tr.order = :order')
                ->setParameter('order', $value);
        }
    }
}
