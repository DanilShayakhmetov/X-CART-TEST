<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Model\Repo;

/**
 * News messages repository
 */
class Shipment extends \XLite\Model\Repo\ARepo
{
    const SEARCH_ORDER_ID = 'order_id';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array|string               $value
     * @param boolean                    $countOnly
     *
     * @return void
     */
    protected function prepareCndOrderId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->andWhere('s.order = :order')
                ->setParameter('order', $value);
        }
    }
}
