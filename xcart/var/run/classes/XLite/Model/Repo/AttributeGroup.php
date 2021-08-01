<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Attribute groups repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\AttributeGroup", summary="Add new attribute group")
 * @Api\Operation\Read(modelClass="XLite\Model\AttributeGroup", summary="Retrieve attribute group by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\AttributeGroup", summary="Retrieve attribute groups by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\AttributeGroup", summary="Update attribute group by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\AttributeGroup", summary="Delete attribute group by id")
 */
class AttributeGroup extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    const SEARCH_PRODUCT_CLASS = 'productClass';
    const SEARCH_NAME          = 'name';

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    // {{{ Search

    /**
     * Search by product class
     *
     * @param type $productClass
     *
     * @return array
     */
    public function findByProductClass($productClass)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->productClass = $productClass;
        return $this->search($cnd);
    }

    /**
     * Prepare certain search condition
     * @Api\Condition(description="Filters attribute groups by product class ids", type="array", collectionFormat="multi", items=@Swg\Items(type="integer"))
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function prepareCndProductClass(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('a.productClass = :productClass')
                ->setParameter('productClass', $value);

        } else {
            $queryBuilder->andWhere('a.productClass is null');
        }
    }

    /**
     * Prepare certain search condition
     * @Api\Condition(description="Filters attribute groups by name", type="string")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        $queryBuilder->andWhere('translations.name = :name')
            ->setParameter('name', $value);
    }

    // }}}

    // {{{ Find one by name

    /**
     * Find entity by name (any language)
     *
     * @param string  $name      Name
     * @param boolean $countOnly Count only OPTIONAL
     *
     * @return \XLite\Model\AttributeGroup|integer
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

    /**
     * Find entity by name (any language) and product class
     *
     * @param string  $name      Name
     * @param \XLite\Model\ProductClass $productClass
     * @param boolean $countOnly Count only OPTIONAL
     *
     * @return \XLite\Model\AttributeGroup|integer
     */
    public function findOneByNameAndProductClass($name, $productClass, $countOnly = false)
    {
        return $countOnly
            ? count($this->defineOneByNameAndProductClassQuery($name, $productClass)->getResult())
            : $this->defineOneByNameAndProductClassQuery($name, $productClass)->getSingleResult();
    }

    /**
     * Define query builder for findOneByNameAndProductClass() method
     *
     * @param string $name Name
     * @param \XLite\Model\ProductClass $productClass
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineOneByNameAndProductClassQuery($name, $productClass)
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('translations.name = :name')
            ->setParameter('name', $name);

        if ($productClass) {
            $qb->andWhere('a.productClass = :productClass')
                ->setParameter('productClass', $productClass);
        } else {
            $qb->andWhere('a.productClass is null');
        }

        return $qb;
    }
}
