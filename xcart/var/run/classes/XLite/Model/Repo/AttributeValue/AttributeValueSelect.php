<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\AttributeValue;

/**
 * Attribute values repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\AttributeValue\AttributeValueSelect", summary="Add new select attribute value")
 * @Api\Operation\Read(modelClass="XLite\Model\AttributeValue\AttributeValueSelect", summary="Retrieve select attribute value by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\AttributeValue\AttributeValueSelect", summary="Retrieve select attribute values by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\AttributeValue\AttributeValueSelect", summary="Update select attribute value by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\AttributeValue\AttributeValueSelect", summary="Delete select attribute value by id")
 */
class AttributeValueSelect extends \XLite\Model\Repo\AttributeValue\Multiple
{
    /**
     * Allowable search params
     */
    const SEARCH_ATTRIBUTE_OPTION  = 'attributeOption';

    /**
     * Postprocess common
     *
     * @param array $data Data
     *
     * @return array
     */
    protected function postprocessCommon(array $data)
    {
        $result = array();

        foreach ($data as $v) {
            if (!isset($result[$v['attrId']])) {
                $result[$v['attrId']] = array();
            }
            $val = $v[0];
            unset($val['id']);
            unset($val['attribute_option_id']);
            $result[$v['attrId']][$v['attrOptionId']] = $val;
        }

        return $result;
    }

    /**
     * Return QueryBuilder for common values
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createQueryBuilderCommonValues(\XLite\Model\Product $product)
    {
        return parent::createQueryBuilderCommonValues($product)
            ->addSelect('ao.id attrOptionId')
            ->innerJoin('av.attribute_option', 'ao');
    }

    /**
     * Prepare certain search condition
     * @Api\Condition(description="Filters attribute values by attribute option", type="integer")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function prepareCndAttributeOption(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere('a.attribute_option = :attributeOption')
                ->setParameter('attributeOption', $value);
        }
    }

    /**
     * Define QueryBuilder for findOneByValue() method
     *
     * @param \XLite\Model\Product   $product   Product object
     * @param \XLite\Model\Attribute $attribute Attribute object
     * @param mixed                  $value     Value
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindOneByValueQuery($product, $attribute, $value)
    {
        $qb = parent::defineFindOneByValueQuery($product, $attribute, $value);

        $attrOption = \XLite\Core\Database::getRepo('XLite\Model\AttributeOption')->findOneByNameAndAttribute($value, $attribute);

        $qb->andWhere('av.attribute_option = :attrOption')
            ->setParameter('attrOption', $attrOption);

        return $qb;
    }

    /**
     * @param \XLite\Model\AttributeOption $option
     */
    public function updatePositionByOption($option)
    {
        $qb = $this->createPureQueryBuilder('a')->update($this->_entityName, 'a');

        $qb->set('a.position', ':position')->setParameter('position', $option->getPosition());
        $qb->where('a.attribute_option = :option')->setParameter('option', $option);

        $qb->execute();
    }

    /**
     * Find attribute value which will be considered as a default if attribute has not specific default value
     *
     * @param array $data Data to search: array('product' => ..., 'attribute' => ...)
     *
     * @return \XLite\Model\AttributeValue\AAttributeValue
     */
    public function findDefaultAttributeValue($data)
    {
        return $this->findOneBy($data, ['position' => 'ASC']);
    }

    /**
     * Define export iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineExportIteratorQueryBuilder($position)
    {
        return parent::defineExportIteratorQueryBuilder($position)
            ->addOrderBy($this->getDefaultAlias().'.position');
    }
}
