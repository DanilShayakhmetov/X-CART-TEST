<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\Model\Repo;

/**
 * The "product" model repository
 */
class Order extends \XLite\Model\Repo\Order implements \XLite\Base\IDecorator
{
    public function getDemoEntitiesCount()
    {
        return $this->createPureQueryBuilder('o')
            ->select('COUNT(o)')
            ->andWhere('o.demo = 1')
            ->getSingleScalarResult();
    }

    public function deleteDemoEntities()
    {
        $this->createPureQueryBuilder('o')
            ->delete($this->_entityName, 'o')
            ->andWhere('o.demo = 1')
            ->getQuery()->execute();
    }
}
