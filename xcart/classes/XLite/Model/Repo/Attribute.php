<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Attributes repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Attribute", summary="Add new product attribute")
 * @Api\Operation\Read(modelClass="XLite\Model\Attribute", summary="Retrieve product attribute by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Attribute", summary="Retrieve product attributes by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\Attribute", summary="Update product attribute by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Attribute", summary="Delete product attribute by id")
 */
class Attribute extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    const SEARCH_PRODUCT          = 'product';
    const SEARCH_PRODUCT_CLASS    = 'productClass';
    const SEARCH_ATTRIBUTE_GROUP  = 'attributeGroup';
    const SEARCH_TYPE             = 'type';
    const SEARCH_NAME             = 'name';

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    /**
     * Find multiple attributes
     *
     * @param \XLite\Model\Product $product Product
     * @param array                $ids     Array of Ids
     *
     * @return array
     */
    public function findMultipleAttributes(\XLite\Model\Product $product, $ids)
    {
        return $ids
            ? $this->defineFindMultipleAttributesQuery($product, $ids)->getResult()
            : array();
    }

    /**
     * Define query for findMultipleAttributes() method
     * 
     * @param \XLite\Model\Product $product Product
     * @param array                $ids     Attribute ID list
     *  
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindMultipleAttributesQuery(\XLite\Model\Product $product, array $ids)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb->leftJoin('a.attribute_properties', 'ap', 'WITH', 'ap.product = :product')
            ->addSelect('ap.position')
            ->addInCondition('a.id', $ids)
            ->addGroupBy('a.id')
            ->setParameter('product', $product);
    }

    /**
     * Define items iterator
     *
     * @param integer $position Position OPTIONAL
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getRemoveGlobalAttributesDataIterator($position = 0)
    {
        return $this->defineRemoveGlobalAttributesDataQueryBuilder($position)
            ->setMaxResults(\XLite\Core\EventListener\RemoveData::CHUNK_LENGTH)
            ->iterate();
    }

    /**
     * Define remove data iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineRemoveGlobalAttributesDataQueryBuilder($position)
    {
        $qb = parent::defineRemoveDataQueryBuilder($position);

        return $qb->andWhere($qb->getMainAlias() . '.product IS NULL');
    }

    /**
     * Count items for remove data
     *
     * @return integer
     */
    public function countForRemoveGlobalAttributesData()
    {
        return (int) $this->defineCountForRemoveGlobalAttributesDataQuery()->getSingleScalarResult();
    }

    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForRemoveGlobalAttributesDataQuery()
    {
        $qb = parent::defineCountForRemoveDataQuery();

        return $qb->andWhere($qb->getMainAlias() . '.product IS NULL');
    }

    /**
     * Prepare certain search condition
     *
     * @Api\Condition(description="Filters attributes by certain product id (leave empty to search attributes without product)", type="integer")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndProduct(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('a.product = :attributeProduct')
                ->setParameter('attributeProduct', $value);

        } else {
            $queryBuilder->andWhere('a.product is null');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @Api\Condition(description="Filters attributes by product class ids", type="array", collectionFormat="multi", items=@Swg\Items(type="integer"))
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndProductClass(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if (is_null($value)) {
            $queryBuilder->andWhere('a.productClass is null');

        } elseif (is_object($value) && 'Doctrine\ORM\PersistentCollection' != get_class($value)) {
            $queryBuilder->andWhere('a.productClass = :productClass')
                ->setParameter('productClass', $value);

        } elseif ($value) {

            $ids = array();
            foreach ($value as $id) {
                if ($id) {
                    $ids[] = is_object($id) ? $id->getId() : $id;
                }
            }

            if ($ids) {
                $queryBuilder->linkInner('a.productClass')
                    ->andWhere($queryBuilder->expr()->in('productClass.id', $ids));
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @Api\Condition(description="Filters attributes by attribute group id (leave empty to search attributes without group)", type="integer")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndAttributeGroup(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('a.attributeGroup = :attributeGroup')
                ->setParameter('attributeGroup', $value);

        } else {
            $queryBuilder->andWhere('a.attributeGroup is null');
        }
    }

    /**
     * Prepare certain search condition
     * @Api\Condition(description="Filters attributes by type", type="string", enum={"S", "T", "C", "H"})
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            if (is_array($value)) {
                $queryBuilder->andWhere('a.type IN (\'' . implode("','", $value) . '\')');

            } else {
                $queryBuilder->andWhere('a.type = :type')
                    ->setParameter('type', $value);
            }
        }
    }

    /**
     * Prepare certain search condition
     * @Api\Condition(description="Filters attributes by exluding type", type="string", enum={"S", "T", "C", "H"})
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndNotType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            if (is_array($value)) {
                $queryBuilder->andWhere('a.type NOT IN (\'' . implode("','", $value) . '\')');

            } else {
                $queryBuilder->andWhere('a.type != :type')
                    ->setParameter('type', $value);
            }
        }
    }

    /**
     * Prepare certain search condition
     * @Api\Condition(description="Filters attributes by name", type="string")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            // Add additional join to translations with default language code
            $this->addDefaultTranslationJoins(
                $queryBuilder,
                $this->getMainAlias($queryBuilder),
                'defaults',
                \XLite::getDefaultLanguage()
            );

            $condition = $queryBuilder->expr()->orX();

            $condition->add('translations.name = :name');
            $condition->add('defaults.name = :name');
            if (\XLite\Core\Translation::DEFAULT_LANGUAGE !== \XLite::getDefaultLanguage()) {
                // Add additional join to translations with default-default ('en' at the moment) language code
                $this->addDefaultTranslationJoins(
                    $queryBuilder,
                    $this->getMainAlias($queryBuilder),
                    'defaultDefaults',
                    'en'
                );
                $condition->add('defaultDefaults.name = :name');
            }

            $queryBuilder->andWhere($condition)
                ->setParameter('name', $value);
        }
    }

    // }}}

    // {{{ Export routines

    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForExportQuery()
    {
        $qb = $this->createPureQueryBuilder();

        return $qb->select(
            'COUNT(DISTINCT ' . $qb->getMainAlias() . '.' . $this->getPrimaryKeyField() . ')'
        );
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
        return $this->createPureQueryBuilder()
            ->setFirstResult($position)
            ->setMaxResults(\XLite\Core\EventListener\Export::CHUNK_LENGTH);
    }

    // }}}

    /**
     * Generate attribute values
     *
     * @param \XLite\Model\Product $product         Product
     * @param boolean              $useProductClass Use product class OPTIONAL
     *
     * @return void
     */
    public function generateAttributeValues(\XLite\Model\Product $product, $useProductClass = null)
    {
        $cnd = new \XLite\Core\CommonCell;
        $cnd->productClass = $useProductClass ? $product->getProductClass() : null;
        $cnd->product = null;
        $cnd->type = array(
            \XLite\Model\Attribute::TYPE_CHECKBOX,
            \XLite\Model\Attribute::TYPE_SELECT,
            \XLite\Model\Attribute::TYPE_TEXT,
            \XLite\Model\Attribute::TYPE_HIDDEN,
        );
        foreach ($this->search($cnd) as $a) {
            $a->addToNewProduct($product);
        }
    }

    /**
     * Get identifiers list for specified query builder object
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder
     * @param string                     $name  Name
     * @param mixed                      $value Value
     *
     * @return void
     */
    protected function addImportCondition(\Doctrine\ORM\QueryBuilder $qb, $name, $value)
    {
        if ('productClass' == $name && is_string($value)) {
            $alias = $qb->getMainAlias();
            $qb->linkInner($alias . '.productClass')
                ->linkInner('productClass.translations', 'productClassTranslations')
                ->andWhere('productClassTranslations.name = :productClass')
                ->setParameter('productClass', $value);

        } else {
            parent::addImportCondition($qb, $name, $value);
        }
    }

    /**
     * @param \XLite\Model\Attribute $attribute
     * @return int
     */
    public function countProductsWithValues(\XLite\Model\Attribute $attribute)
    {
        $valuesRepo = \XLite\Core\Database::getRepo(\XLite\Model\Attribute::getAttributeValueClass($attribute->getType()));
        $qb = $valuesRepo->createPureQueryBuilder();
        $alias = $qb->getMainAlias();
        $qb->select('COUNT (DISTINCT ' . $alias . '.product)')
            ->where($alias . '.attribute = :attribute')
            ->setParameter('attribute', $attribute);

        return intval($qb->getSingleScalarResult());
    }

    public function getAttributesWithValues(\XLite\Model\Product $product, $type)
    {
        $attributeValueClass = \XLite\Model\Attribute::getAttributeValueClass($type);

        if (class_exists($attributeValueClass)) {
            $qb = $this->createQueryBuilder();
            $alias = $qb->getMainAlias();
            $qb->join(
                $attributeValueClass,
                'av',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'av.attribute = ' . $alias
            );
            $qb->andWhere('av.product = :product')
                ->setParameter('product', $product);

            if (is_a($attributeValueClass, \XLite\Model\AttributeValue\AttributeValueText::class, true)) {
                $qb->linkLeft('av.translations', 'av_translations')
                    ->andWhere('av.editable = :true OR av_translations.value != :empty')
                    ->setParameter('true', true)
                    ->setParameter('empty', '');
            }

            $data = $qb->getResult();

            return $data;
        }

        return [];
    }
}
