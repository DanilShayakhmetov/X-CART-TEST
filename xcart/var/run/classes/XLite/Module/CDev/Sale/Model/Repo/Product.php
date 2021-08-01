<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Model\Repo;

/**
 * The Product model repository extension
 */
 class Product extends \XLite\Module\XC\BulkEditing\Model\Repo\Product implements \XLite\Base\IDecorator
{
    /**
     * Allowable search params
     */
    const P_PARTICIPATE_SALE = 'participateSale';
    const P_SALE_DISCOUNT = 'saleDiscount';

    /**
     * Name of the calculated field - percent value.
     */
    const PERCENT_CALCULATED_FIELD = 'percentValueCalculated';


    // {{{ Search functionallity extension

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    Count only flag
     *
     * @return void
     */
    protected function prepareCndParticipateSale(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $cnd = new \Doctrine\ORM\Query\Expr\Orx();

        $pricePercentCnd = new \Doctrine\ORM\Query\Expr\Andx();

        $pricePercentCnd->add('p.discountType = :discountTypePercent');
        $pricePercentCnd->add('p.salePriceValue > 0');

        $priceAbsoluteCnd = new \Doctrine\ORM\Query\Expr\Andx();

        $priceAbsoluteCnd->add('p.discountType = :discountTypePrice');
        $priceAbsoluteCnd->add('p.price > p.salePriceValue');

        $cnd->add($pricePercentCnd);
        $cnd->add($priceAbsoluteCnd);

        if (!$countOnly) {
            $queryBuilder->addSelect(
                'if(p.discountType = :discountTypePercent, p.salePriceValue, 100 - 100 * p.salePriceValue / p.price) ' . static::PERCENT_CALCULATED_FIELD
            );
        }

        $queryBuilder->andWhere('p.participateSale = :participateSale')
            ->andWhere($cnd)
            ->setParameter('participateSale', $value)
            ->setParameter('discountTypePercent', \XLite\Module\CDev\Sale\Model\Product::SALE_DISCOUNT_TYPE_PERCENT)
            ->setParameter('discountTypePrice', \XLite\Module\CDev\Sale\Model\Product::SALE_DISCOUNT_TYPE_PRICE);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    Count only flag
     *
     * @return void
     */
    protected function prepareCndSaleDiscount(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $queryBuilder->andWhere('p.participateSale = false');

        if ($value instanceof \XLite\Module\CDev\Sale\Model\SaleDiscount) {
            if ($value->getSpecificProducts()) {
                $queryBuilder->linkLeft('p.saleDiscountProducts', 'sdp');
                $queryBuilder->andWhere('sdp.saleDiscount = :saleDiscount')
                    ->setParameter('saleDiscount', $value);

            } else {
                if (!$value->getCategories()->isEmpty()) {
                    $queryBuilder->linkLeft('p.categoryProducts', 'cp')
                        ->linkLeft('cp.category', 'c');
                    $queryBuilder->andWhere('c IN (:saleCategories)')
                        ->setParameter('saleCategories', $value->getCategories());
                }
                if (!$value->getProductClasses()->isEmpty()) {
                    $queryBuilder->andWhere('p.productClass IN (:saleProductClasses)')
                        ->setParameter('saleProductClasses', $value->getProductClasses());
                }
            }
        }
    }
}
