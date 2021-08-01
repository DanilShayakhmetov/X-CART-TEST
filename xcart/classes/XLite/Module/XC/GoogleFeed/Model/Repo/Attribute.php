<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\Model\Repo;

/**
 * Products repository
 */
abstract class Attribute extends \XLite\Model\Repo\Attribute implements \XLite\Base\IDecorator
{
    /**
     * @param $ids
     * @param string $group
     */
    public function updateGroupInBatch($ids, $group = null)
    {
        $qb = $this->createPureQueryBuilder();
        $alias = $qb->getMainAlias();
        $qb->update('XLite\Model\Attribute', $alias)
            ->set("{$alias}.googleShoppingGroup", ':group')
            ->andWhere($qb->expr()->in("{$alias}.id", $ids))
            ->setParameter('group', $group);

        $qb->execute();
    }

    /**
     * @return array
     */
    public function getUsedGoogleGroupNames()
    {
        $qb = $this->createPureQueryBuilder();
        $alias = $qb->getMainAlias();
        $qb->select("DISTINCT {$alias}.googleShoppingGroup");

        $result = $qb->getResult();

        $result = array_map(function ($elem) {
            return $elem['googleShoppingGroup'];
        }, $result);
        $result = array_filter($result, function ($elem) {
            return !is_null($elem) && '' !== $elem;
        });

        return $result;
    }

    /**
     * Find multiple attributes
     *
     * @param \XLite\Model\Product $product Product
     * @param array                $ids     Array of Ids
     *
     * @return array
     */
    public function getAttributesFeedData(\XLite\Model\Product $product, $ids)
    {
        $qb = $this->defineFindMultipleAttributesQuery($product, $ids);
        $qb->addSelect('a.googleShoppingGroup');

        return $ids
            ? $qb->getResult()
            : [];
    }
}
