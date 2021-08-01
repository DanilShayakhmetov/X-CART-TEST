<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\Model\Repo;

/**
 * @Api\Operation\Create(modelClass="XLite\Module\CDev\FeaturedProducts\Model\FeaturedProduct", summary="Add featured product")
 * @Api\Operation\Read(modelClass="XLite\Module\CDev\FeaturedProducts\Model\FeaturedProduct", summary="Retrieve featured product by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Module\CDev\FeaturedProducts\Model\FeaturedProduct", summary="Retrieve featured products by conditions")
 * @Api\Operation\Update(modelClass="XLite\Module\CDev\FeaturedProducts\Model\FeaturedProduct", summary="Update featured product by id")
 * @Api\Operation\Delete(modelClass="XLite\Module\CDev\FeaturedProducts\Model\FeaturedProduct", summary="Delete featured product by id")
 *
 * @SWG\Tag(
 *   name="CDev\FeaturedProducts\FeaturedProduct",
 *   x={"display-name": "FeaturedProduct", "group": "CDev\FeaturedProducts"},
 *   description="FeaturedProduct represents the presence of the product marked as featured in the certain category",
 *   @SWG\ExternalDocumentation(
 *     description="Find out more about featured products",
 *     url="https://kb.x-cart.com/en/products/adding_featured_products.html"
 *   )
 * )
 */
class FeaturedProduct extends \XLite\Model\Repo\ARepo
{
    // {{{ Search

    const SEARCH_CATEGORY_ID = 'categoryId';

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'orderBy';


    /**
     * Get featured products list
     *
     * @param integer $categoryId Category ID
     *
     * @return array(\XLite\Module\CDev\FeaturedProducts\Model\FeaturedProduct) Objects
     */
    public function getFeaturedProducts($categoryId)
    {
        return $this->findByCategoryId($categoryId);
    }

    /**
     * Find by type
     *
     * @param integer $categoryId Category ID
     *
     * @return array
     */
    protected function findByCategoryId($categoryId)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{static::SEARCH_CATEGORY_ID} = $categoryId;
        return $this->search($cnd);
    }

    /**
     * Prepare certain search condition
     *
     * @Api\Condition(description="Filters featured products by category id", type="integer")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndCategoryId(\Doctrine\ORM\QueryBuilder $qb, $value)
    {
        $f = $this->getMainAlias($qb);
        $qb = $qb->innerJoin($f . '.product', 'p')
            ->andWhere($f . '.category = :categoryId')
            ->setParameter('categoryId', $value);

        return \XLite\Core\Database::getRepo('XLite\Model\Product')->assignExternalEnabledCondition($qb, 'p');
    }

    // }}}
}
