<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomOrderStatuses\Model\Repo;

/**
 * The Order model repository extension
 *
 * @Decorator\After ("XC\CustomOrderStatuses")
 * @Decorator\Depend ("XC\NotFinishedOrders")
 */
abstract class OrderNFO extends \XLite\Model\Repo\Order implements \XLite\Base\IDecorator
{
    /**
     * @param $statusType
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountByStatusQuery($statusType)
    {
        $qb = parent::defineCountByStatusQuery($statusType);

        return 0 === strpos($statusType, 'shipping')
            ? $this->addNotFinishedCnd($qb)
            : $qb;
    }
}