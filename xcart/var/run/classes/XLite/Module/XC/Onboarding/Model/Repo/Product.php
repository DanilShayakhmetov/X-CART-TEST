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
 class Product extends \XLite\Module\XC\ProductTags\Model\Repo\Product implements \XLite\Base\IDecorator
{
    public function getDemoEntitiesCount()
    {
        return $this->createPureQueryBuilder('p')
            ->select('COUNT(p)')
            ->andWhere('p.demo = 1')
            ->getSingleScalarResult();
    }

    public function deleteDemoEntities()
    {
        $this->createPureQueryBuilder('p')
            ->delete($this->_entityName, 'p')
            ->andWhere('p.demo = 1')
            ->execute();
    }
}
