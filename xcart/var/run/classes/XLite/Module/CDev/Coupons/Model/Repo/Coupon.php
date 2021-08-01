<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\Model\Repo;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * @Api\Operation\Create(modelClass="XLite\Module\CDev\Coupons\Model\Coupon", summary="Add new coupon")
 * @Api\Operation\Read(modelClass="XLite\Module\CDev\Coupons\Model\Coupon", summary="Retrieve coupon by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Module\CDev\Coupons\Model\Coupon", summary="Retrieve coupons by conditions")
 * @Api\Operation\Update(modelClass="XLite\Module\CDev\Coupons\Model\Coupon", summary="Update coupon by id")
 * @Api\Operation\Delete(modelClass="XLite\Module\CDev\Coupons\Model\Coupon", summary="Delete coupon by id")
 *
 * @SWG\Tag(
 *   name="CDev\Coupons\Coupon",
 *   x={"display-name": "Coupon", "group": "CDev\Coupons"},
 *   description="Coupon represents the code which can be given to customers to activate a discount. Coupon repo stores only unused codes. See UsedCoupon to check for coupons with the order relation.",
 *   @SWG\ExternalDocumentation(
 *     description="Find out more about discount coupons",
 *     url="https://kb.x-cart.com/seo_and_promotion/setting_up_discount_coupons.html"
 *   )
 * )
 */
class Coupon extends \XLite\Model\Repo\ARepo
{
    use ExecuteCachedTrait;

    // {{{ Find duplicates

    /**
     * Find duplicates
     *
     * @param string                                  $code          Code
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $currentCoupon Current coupon OPTIONAL
     *
     * @return array
     */
    public function findDuplicates($code, \XLite\Module\CDev\Coupons\Model\Coupon $currentCoupon = null)
    {
        return $this->defineFindDuplicatesQuery($code, $currentCoupon)->getResult();
    }

    /**
     * Define query for findDuplicates()
     *
     * @param string                                  $code          Code
     * @param \XLite\Module\CDev\Coupons\Model\Coupon $currentCoupon Current coupon OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindDuplicatesQuery($code, \XLite\Module\CDev\Coupons\Model\Coupon $currentCoupon = null)
    {
        $queryBuilder = $this->createPureQueryBuilder('c')
            ->andWhere('COLLATE(c.code, ' . \XLite\Core\Database::getCharset() . '_bin) = :code')
            ->setParameter('code', $code);

        if ($currentCoupon) {
            $queryBuilder->andWhere('c.id != :cid')
                ->setParameter('cid', $currentCoupon->getId());
        }

        return $queryBuilder;
    }

    // }}}

    // {{{ Find by code

    /**
     * Find duplicates
     *
     * @param string $code Code
     *
     * @return null|\XLite\Model\AEntity
     */
    public function findOneByCode($code)
    {
        return $this->defineFindOneByCode($code)->getSingleResult();
    }

    /**
     * Define query for findDuplicates()
     *
     * @param string $code Code
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneByCode($code)
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->andWhere('COLLATE(c.code, ' . \XLite\Core\Database::getCharset() . '_bin) = :code')
            ->setParameter('code', $code);

        return $queryBuilder;
    }

    // }}}

    public function findAllProductSpecific()
    {
        return $this->executeCachedRuntime(function() {
            $qb = $this->createQueryBuilder('c')
                ->andWhere('c.specificProducts = :specificProducts')
                ->setParameter('specificProducts', true);

            return $qb->getResult();
        });
    }
}
