<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\Model\Repo;

use XLite\Module\CDev\PINCodes\Model\PinCode as PinCodeModel;

/**
 * @Api\Operation\Create(modelClass="XLite\Module\CDev\PINCodes\Model\PinCode", summary="Add pincode")
 * @Api\Operation\Read(modelClass="XLite\Module\CDev\PINCodes\Model\PinCode", summary="Retrieve pincode by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Module\CDev\PINCodes\Model\PinCode", summary="Retrieve pincodes by conditions")
 * @Api\Operation\Update(modelClass="XLite\Module\CDev\PINCodes\Model\PinCode", summary="Update pincode by id")
 * @Api\Operation\Delete(modelClass="XLite\Module\CDev\PINCodes\Model\PinCode", summary="Delete pincode by id")
 *
 * @SWG\Tag(
 *   name="CDev\PINCodes\PinCode",
 *   x={"display-name": "PinCode", "group": "CDev\PINCodes"},
 *   description="Pincode allows to attach some text information (e.g. CD key, product registration code) to the product and sell it within"
 * )
 */
class PinCode extends \XLite\Model\Repo\ARepo
{
    /**
     * Prepare certain search condition
     *
     * @Api\Condition(description="Filters pincodes by certain product id", type="integer")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndProduct(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder
            ->andWhere('p.product=:product')
            ->setParameter('product', $value);
    }

    /**
     * Counts sold pin codes by product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return integer
     */
    public function countSold(\XLite\Model\Product $product)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.product = :product AND p.isSold = :isSold')
            ->setParameter('isSold', true)
            ->setParameter('product', $product)
            ->count();
    }

    /**
     * Counts blocked pin codes by product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return integer
     */
    public function countBlocked(\XLite\Model\Product $product)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.product = :product AND (p.isSold = :isSold OR p.isBlocked = :isBlocked)')
            ->setParameter('isSold', true)
            ->setParameter('isBlocked', true)
            ->setParameter('product', $product)
            ->count();
    }

    /**
     * Counts sold pin codes by product
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return integer
     */
    public function countRemaining(\XLite\Model\Product $product)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.product = :product AND p.isSold = :isSold AND p.isBlocked = :isBlocked')
            ->setParameter('isSold', false)
            ->setParameter('isBlocked', false)
            ->setParameter('product', $product)
            ->count();
    }

    protected function getUnavailablePinCodesIds()
    {
        $im = \XLite\Core\Database::getEM()->getUnitOfWork()->getIdentityMap();

        if (isset($im[$this->getEntityName()])) {
            return array_map(function (PinCodeModel $pinCode) {
                return $pinCode->getId();
            }, array_filter($im[$this->getEntityName()], function (PinCodeModel $pinCode) {
                return $pinCode->getIsSold() || $pinCode->getIsBlocked();
            }));
        }

        return [];
    }

    /**
     * Returns not sold pin code 
     *
     * @param \XLite\Model\Product $product Product
     * @param integer              $count   Count
     *
     * @return PinCodeModel[]
     */
    public function getAvailablePinCodes(\XLite\Model\Product $product, $count)
    {
        $qb = $this->createQueryBuilder('p');

        $qb->andWhere('p.product = :product AND p.isSold = :isSold AND p.isBlocked = :isBlocked')
            ->addOrderBy('p.id')
            ->setParameter('isSold', false)
            ->setParameter('isBlocked', false)
            ->setParameter('product', $product)
            ->setMaxResults($count);

        if ($ids = $this->getUnavailablePinCodesIds()) {
            $qb->andWhere($qb->expr()->notIn(
                'p.id',
                $ids
            ));
        }

        return $qb->getResult();
    }
}
