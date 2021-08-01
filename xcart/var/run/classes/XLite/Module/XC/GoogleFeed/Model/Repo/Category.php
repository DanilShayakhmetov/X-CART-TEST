<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\Model\Repo;

/**
 * Category repository
 */
abstract class Category extends \XLite\Module\XC\Onboarding\Model\Repo\Category implements \XLite\Base\IDecorator
{
    /**
     * Define sitemap generation iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFeedGenerationQueryBuilder($position)
    {
        $qb = parent::defineFeedGenerationQueryBuilder($position);

        $qb->andWhere($qb->getMainAlias() . '.parent IS NOT NULL');

        return $qb;
    }
}

