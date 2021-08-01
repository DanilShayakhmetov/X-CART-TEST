<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Model\Repo;

/**
 * @Api\Operation\Create(modelClass="XLite\Module\CDev\Wholesale\Model\WholesalePrice", summary="Add wholesale price tier")
 * @Api\Operation\Read(modelClass="XLite\Module\CDev\Wholesale\Model\WholesalePrice", summary="Retrieve wholesale price tier by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Module\CDev\Wholesale\Model\WholesalePrice", summary="Retrieve wholesale price tiers by conditions")
 * @Api\Operation\Update(modelClass="XLite\Module\CDev\Wholesale\Model\WholesalePrice", summary="Update wholesale price tier by id")
 * @Api\Operation\Delete(modelClass="XLite\Module\CDev\Wholesale\Model\WholesalePrice", summary="Delete wholesale price tier by id")
 *
 * @SWG\Tag(
 *   name="CDev\Wholesale\WholesalePrice",
 *   x={"display-name": "WholesalePrice", "group": "CDev\Wholesale"},
 *   description="WholesalePrice record keeps data about wholesale discount tiers of a certain product and membership",
 *   @SWG\ExternalDocumentation(
 *     description="Find out more about setting up wholesale price and minimum purchase quantities",
 *     url="https://kb.x-cart.com/en/products/setting_up_wholesale_prices_and_minimum_purchase_quantites_for_different_membership_levels.html"
 *   )
 * )
 */
class WholesalePrice extends \XLite\Module\CDev\Wholesale\Model\Repo\Base\AWholesalePrice
{
    /**
     * Allowable search params
     */
    const P_PRODUCT = 'product';

    /**
     * Get modifier types by product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return array
     */
    public function getModifierTypesByProduct(\XLite\Model\Product $product)
    {
        $price = $this->createQueryBuilder('w')
            ->andWhere('w.product = :product')
            ->setParameter('product', $product)
            ->setMaxResults(1)
            ->getResult();

        return [
            'price'          => !empty($price),
            'wholesalePrice' => !empty($price),
        ];
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndProduct(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value instanceOf \XLite\Model\Product) {
            $queryBuilder->andWhere('w.product = :product')
                ->setParameter('product', $value);

        } else {
            $queryBuilder->leftJoin('w.product', 'product')
                ->andWhere('product.product_id = :productId')
                ->setParameter('productId', $value);
        }
    }

    /**
     * Process contition 
     *
     * @param \XLite\Core\CommonCell $cnd    Contition
     * @param mixed                  $object Object
     *
     * @return \XLite\Core\CommonCell
     */
    protected function processContition($cnd, $object)
    {
        $cnd->{self::P_PRODUCT} = $object;

        return $cnd;
    }
}
