<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Model\Repo;

/**
 * The Product model repository extension
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
class ProductVariant extends \XLite\Module\XC\ProductVariants\Model\Repo\ProductVariant implements \XLite\Base\IDecorator
{
    /**
     * Get variants count by product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return string
     */
    public function getOnSaleVariantsCountByProduct(\XLite\Model\Product $product)
    {
        return $this->createQueryBuilder('v')
            ->selectCount()
            ->andWhere('v.product = :product')
            ->andWhere('v.defaultSale = 0')
            ->setParameter('product', $product)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get modifier types by product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return array
     */
    public function getModifierTypesByProduct(\XLite\Model\Product $product)
    {
        $modifierTypes = parent::getModifierTypesByProduct($product);

        if (!isset($modifierTypes['price']) || !$modifierTypes['price']) {
            $price = $this->createQueryBuilder('v')
                ->andWhere('v.product = :product AND v.defaultSale = :false')
                ->setParameter('product', $product)
                ->setParameter('false', false)
                ->setMaxResults(1)
                ->getResult();

            $modifierTypes['price'] = !empty($price);
        }

        return $modifierTypes;
    }
}
