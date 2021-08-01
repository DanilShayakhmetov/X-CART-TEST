<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Attribute options repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\AttributeOption", summary="Add new attribute option")
 * @Api\Operation\Read(modelClass="XLite\Model\AttributeOption", summary="Retrieve attribute option by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\AttributeOption", summary="Retrieve attribute options by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\AttributeOption", summary="Update attribute option by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\AttributeOption", summary="Delete attribute option by id")
 */
class AttributeOption extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    const SEARCH_ATTRIBUTE = 'attribute';
    const SEARCH_NAME      = 'name';

    /**
     * Find one option by name and attribute
     *
     * @param string                 $name      Name
     * @param \XLite\Model\Attribute $attribute Attribute
     *
     * @return \XLite\Model\AttributeOption
     */
    public function findOneByNameAndAttribute($name, \XLite\Model\Attribute $attribute)
    {
        return $this->createPureQueryBuilder('a')
            ->linkLeft('a.translations', 'translations')
            ->andWhere('translations.name = :name')
            ->andWhere('a.attribute = :attribute')
            ->setParameter('name', $name)
            ->setParameter('attribute', $attribute)
            ->setMaxResults(1)
            ->getSingleResult();
    }

    /**
     * Prepare certain search condition
     * @Api\Condition(description="Filters attribute options by attribute id", type="integer")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndAttribute(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            if (is_object($value)) {
                $queryBuilder->andWhere('a.attribute = :attribute');

            } else {
                $queryBuilder->linkInner('a.attribute')->andWhere('attribute.id = :attribute');
            }
            $queryBuilder->setParameter('attribute', $value);
        }
    }

    /**
     * Prepare certain search condition
     * @Api\Condition(description="Filters attribute options by name", type="string")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('translations.name LIKE :name')
                ->setParameter('name', '%' . $value . '%');
        }
    }

    /**
     * @param \XLite\Model\Attribute $attribute
     */
    public function resetAddToNew($attribute)
    {
        $qb = $this->createPureQueryBuilder('ao')->update($this->_entityName, 'ao');

        $qb->set('ao.addToNew', ':addToNew')->setParameter('addToNew', false);
        $qb->where('ao.attribute = :attribute')->setParameter('attribute', $attribute);

        $qb->execute();
    }

}
