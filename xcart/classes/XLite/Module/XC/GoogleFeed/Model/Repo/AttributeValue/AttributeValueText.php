<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\Model\Repo\AttributeValue;

/**
 * Attribute values repository
 */
class AttributeValueText extends \XLite\Model\Repo\AttributeValue\AttributeValueText implements \XLite\Base\IDecorator
{
    /**
     * Find editable attributes of product
     *
     * @param \XLite\Model\Product $product Product object
     *
     * @return array
     */
    public function findNonEditableAttributesGoogleFeed(\XLite\Model\Product $product)
    {
        $data = $this->createQueryBuilder('av')
            ->select('a.id')
            ->addSelect('COUNT(a.id) cnt')
            ->innerJoin('av.attribute', 'a')
            ->andWhere('av.product = :product AND av.editable = :false')
            ->andWhere('a.productClass is null OR a.productClass = :productClass')
            ->andWhere('a.googleShoppingGroup IN (:groups)')
            ->setParameter('product', $product)
            ->setParameter('productClass', $product->getProductClass())
            ->setParameter('groups', \XLite\Model\Attribute::getGoogleShoppingGroups())
            ->setParameter('false', false)
            ->addGroupBy('a.id')
            ->addOrderBy('a.position', 'ASC')
            ->getResult();

        $ids = [];
        if ($data) {
            foreach ($data as $v) {
                $ids[] = $v['id'];
            }
        }

        return \XLite\Core\Database::getRepo('XLite\Model\Attribute')->getAttributesFeedData($product, $ids);
    }
}
