<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Product classes repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\ProductClass", summary="Add new product class")
 * @Api\Operation\Read(modelClass="XLite\Model\ProductClass", summary="Retrieve product class by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\ProductClass", summary="Retrieve product classs by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\ProductClass", summary="Update product class by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\ProductClass", summary="Delete product class by id")
 */
class ProductClass extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    /**
     * Allowable search params
     */
    const CND_PRODUCT = 'product';

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndProduct(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value && !is_object($value)) {
            $ids = array();
            foreach ($value as $product) {
                if (
                    $product
                    && is_object($product)
                    && $product->getProductClass()
                ) {
                    $ids[$product->getProductClass()->getId()] = $product->getProductClass()->getId();
                }
            }

            if ($ids) {
                $queryBuilder->andWhere($queryBuilder->expr()->in('p.id', $ids));

            } else {
                $queryBuilder->andWhere('p.id is null');
            }
        }
    }

    // }}}

    // {{{ Find one by name

    /**
     * Find entity by name (any language)
     *
     * @param string  $name      Name
     * @param boolean $countOnly Count only OPTIONAL
     *
     * @return \XLite\Model\ProductClass|integer
     */
    public function findOneByName($name, $countOnly = false)
    {
        return $countOnly
            ? count($this->defineOneByNameQuery($name)->getResult())
            : $this->defineOneByNameQuery($name)->getSingleResult();
    }

    /**
     * Define query builder for findOneByName() method
     *
     * @param string $name Name
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineOneByNameQuery($name)
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('translations.name = :name')
            ->setParameter('name', $name);

        return $qb;
    }

    // }}}
}
