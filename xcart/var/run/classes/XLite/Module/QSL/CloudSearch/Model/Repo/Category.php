<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Model\Repo;

use XLite\Core\Request;


/**
 * Category repository class
 */
 class Category extends \XLite\Module\QSL\FlyoutCategoriesMenu\Model\Repo\Category implements \XLite\Base\IDecorator
{
    const P_CLOUD_SEARCH_CATEGORY_IDS = 'cloudSearchCategoryIds';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     */
    protected function prepareCndCloudSearchCategoryIds(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere($queryBuilder->expr()->in('c.category_id', $value));
    }
}
