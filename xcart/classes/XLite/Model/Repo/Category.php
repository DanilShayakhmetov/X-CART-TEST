<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Category repository class
 *
 * @Api\Operation\Create(modelClass="XLite\Model\Category", summary="Add new category")
 * @Api\Operation\Read(modelClass="XLite\Model\Category", summary="Retrieve category by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\Category", summary="Retrieve categories by conditions")
 * @Api\Operation\Update(modelClass="XLite\Model\Category", summary="Update category by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\Category", summary="Delete category by id")
 */
class Category extends \XLite\Model\Repo\Base\I18n
{
    use ExecuteCachedTrait;

    /**
     * Allowable search params
     */
    const SEARCH_PARENT  = 'parent';
    const SEARCH_SUBTREE = 'subTree';

    const ROOT_LPOS = 1;

    /**
     * Index for subtree param names
     *
     * @var integer
     */
    static protected $subTreeConditionIndex = 0;

    /**
     * Maximum value of the "rpos" field in all records
     *
     * @var integer
     */
    protected $maxRightPos;

    /**
     * Flush unit-of-work changes after every record loading
     *
     * @var boolean
     */
    protected $flushAfterLoading = true;

    /**
     * Root category
     *
     * @var \XLite\Model\Category
     */
    protected static $rootCategory;

    /**
     * Return the reserved ID of root category
     *
     * @param boolean $override Override flag OPTIONAL
     *
     * @return \XLite\Model\Category
     */
    public function getRootCategory($override = false)
    {
        if ($override || !isset(static::$rootCategory)) {
            static::$rootCategory = $this->findOneByLpos(static::ROOT_LPOS) ?: false;

            if (!static::$rootCategory) {
                static::$rootCategory = $this->findOneByParent(null) ?: false;
            }

            if (!static::$rootCategory) {
                static::$rootCategory = $this->findOneByDepth(-1) ?: false;
            }
        }

        return static::$rootCategory ?: null;
    }

    /**
     * Return the reserved ID of root category
     *
     * @return integer|null
     */
    public function getRootCategoryId()
    {
        $category = $this->getRootCategory();

        return $category ? $category->getCategoryId() : null;
    }

    /**
     * Return the category enabled condition
     *
     * @return boolean
     */
    public function getEnabledCondition()
    {
        return !\XLite::isAdminZone();
    }

    /**
     * Return the category membership condition
     *
     * @return boolean
     */
    public function getMembershipCondition()
    {
        return !\XLite::isAdminZone();
    }

    /**
     * Create a new QueryBuilder instance that is prepopulated for this entity name
     *
     * @param string  $alias       Table alias OPTIONAL
     * @param string  $indexBy     The index for the from. OPTIONAL
     * @param string  $code        Language code OPTIONAL
     * @param boolean $excludeRoot Do not include root category into the search result OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function createQueryBuilder($alias = null, $indexBy = null, $code = null, $excludeRoot = true)
    {
        $queryBuilder = parent::createQueryBuilder($alias, $indexBy, $code);

        return $this->initializeQueryBuilder($queryBuilder, $alias, $excludeRoot);
    }

    /**
     * Initialize the query builder (to prevent the use of language query)
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to use
     * @param string                     $alias        Table alias OPTIONAL
     * @param boolean                    $excludeRoot  Do not include root category into the search result OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function initializeQueryBuilder($queryBuilder, $alias = null, $excludeRoot = true)
    {
        $this->addEnabledCondition($queryBuilder, $alias);
        $this->addOrderByCondition($queryBuilder, $alias);
        $this->addMembershipCondition($queryBuilder, $alias);

        if ($excludeRoot) {
            $this->addExcludeRootCondition($queryBuilder, $alias);
        }

        return $queryBuilder;
    }

    /**
     * find() with cache
     *
     * @param integer $categoryId Category ID
     *
     * @return \XLite\Model\Category
     */
    public function getCategory($categoryId)
    {
        return $this->find($this->prepareCategoryId($categoryId));
    }

    /**
     * Return full list of categories
     *
     * @param integer $rootId ID of the subtree root OPTIONAL
     *
     * @return array
     */
    public function getCategories($rootId = null)
    {
        return $this->defineFullTreeQuery($rootId)->getResult();
    }

    /**
     * Return full list of categories
     *
     * @param integer $rootId ID of the subtree root OPTIONAL
     *
     * @return array
     */
    public function getCategoriesPlainList($rootId = null)
    {
        $rootId = $rootId ?: $this->getRootCategoryId();

        return $this->getCategoriesPlainListChild($rootId);
    }

    /**
     * Return list of subcategories (one level)
     *
     * @param integer $rootId ID of the subtree root
     *
     * @return array
     */
    public function getSubcategories($rootId)
    {
        return $this->defineSubcategoriesQuery($rootId)->getResult();
    }

    /**
     * Return list of categories on the same level
     *
     * @param \XLite\Model\Category $category Category
     * @param boolean               $hasSelf  Flag to include itself OPTIONAL
     *
     * @return array
     */
    public function getSiblings(\XLite\Model\Category $category, $hasSelf = false)
    {
        return $this->defineSiblingsQuery($category, $hasSelf)->getResult();
    }

    /**
     * Return framed list of categories on the same level
     *
     * @param \XLite\Model\Category $category   Category
     * @param integer               $maxResults Max results
     * @param boolean               $hasSelf    Flag to include itself OPTIONAL
     *
     * @return array
     */
    public function getSiblingsFramed(\XLite\Model\Category $category, $maxResults, $hasSelf = false)
    {
        return $this->defineSiblingsFramedQuery($category, $maxResults, $hasSelf)->getResult();
    }

    /**
     * Return categories subtree
     *
     * @param integer $categoryId Category Id
     *
     * @return array
     */
    public function getSubtree($categoryId)
    {
        return $category = $this->getCategory($categoryId)
            ? $this->defineSubtreeQuery($categoryId)->getResult()
            : [];
    }

    /**
     * Get categories path from root to the specified category
     *
     * @param integer $categoryId Category Id
     *
     * @return array
     */
    public function getCategoryPath($categoryId)
    {
        return $category = $this->getCategory($categoryId)
            ? $this->defineCategoryPathQuery($categoryId)->getResult()
            : [];
    }

    /**
     * Return the array of the category path
     *
     * @param integer $categoryId Category Id
     *
     * @return array
     */
    public function getCategoryNamePath($categoryId)
    {
        return array_map([$this, 'getCategoryName'], $this->getCategoryPath($categoryId));
    }

    /**
     * The method is used as a callback in the "$this->getCategoryNamePath()" method
     *
     * @param \XLite\Model\Category $category Category
     *
     * @return string
     */
    public function getCategoryName(\XLite\Model\Category $category)
    {
        return $category->getName();
    }

    /**
     * Get depth of the category path
     *
     * @param integer $categoryId Category Id
     *
     * @return integer
     */
    public function getCategoryDepth($categoryId)
    {
        return $category = $this->getCategory($categoryId)
            ? $this->defineCategoryDepthQuery($categoryId)->getSingleScalarResult()
            : 0;
    }

    /**
     * Get categories list by product ID
     *
     * @param integer $productId Product ID
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function findAllByProductId($productId)
    {
        return $this->defineSearchByProductIdQuery($productId)->getResult();
    }

    /**
     * Find one by path
     *
     * @param array   $path   Category path
     * @param boolean $strict Flag: true - use strict match categories by name,
     *                        false - suggest &amp; and & as the same string
     * @param null    $root
     *
     * @return \XLite\Model\Category
     */
    public function findOneByPath(array $path, $strict = true, $root = null)
    {
        $result = $root ?: $this->getRootCategory();

        if (!empty($path)) {
            do {
                $name = array_shift($path);

                $qb = $this->createQueryBuilder()
                    ->andWhere('c.parent = :parent')
                    ->setParameter('parent', $result);

                $names = [];

                if (!$strict) {
                    $names[] = $name;
                    $names[] = html_entity_decode($name);
                    $names[] = htmlspecialchars($name);
                    $names = array_unique($names);
                }

                if (1 < count($names)) {

                    $orCnd = new \Doctrine\ORM\Query\Expr\Orx();

                    foreach ($names as $k => $v) {
                        $orCnd->add('translations.name = :name' . $k);
                        $qb->setParameter('name' . $k, $v);
                    }

                    $qb->andWhere($orCnd);

                } else {
                    $qb->andWhere('translations.name = :name')
                        ->setParameter('name', $name);
                }

                $result = $qb->getSingleResult();

            } while ($result && $path);
        }

        return $result;
    }

    /**
     * Find all by path
     *
     * @param array   $path   Category path
     * @param boolean $strict Flag: true - use strict match categories by name,
     *                        false - suggest &amp; and & as the same string
     * @param null    $root
     *
     * @return [\XLite\Model\Category[], \XLite\Model\Category]
     */
    public function findAllByPath(array $path, $strict = true, $root = null)
    {
        if ($root) {
            $result = $root;
            $completeResult = [];
        } else {
            $result = $this->getRootCategory();
            $completeResult = [$result->getCategoryId() => $result];
        }

        if (!empty($path)) {
            do {
                $name = array_shift($path);

                $qb = $this->createQueryBuilder()
                    ->andWhere('c.parent = :parent')
                    ->setParameter('parent', $result);

                $names = [];

                if (!$strict) {
                    $names[] = $name;
                    $names[] = html_entity_decode($name);
                    $names[] = htmlspecialchars($name);
                    $names   = array_unique($names);
                }

                $defaultLanguage = 'en';

                if ($this->getTranslationCode() !== $defaultLanguage) {
                    // Add additional join to translations with default language code (en)
                    $this->addDefaultTranslationJoins(
                        $qb,
                        $this->getMainAlias($qb),
                        'default',
                        $defaultLanguage
                    );
                }

                if (1 < count($names)) {

                    $orCnd = new \Doctrine\ORM\Query\Expr\Orx();

                    foreach ($names as $k => $v) {
                        if ($this->getTranslationCode() !== $defaultLanguage) {
                            $orCnd->add('(CASE WHEN translations.name IS NOT NULL
                                THEN translations.name
                                ELSE default.name END) = :name' . $k);
                        } else {
                            $orCnd->add('translations.name = :name' . $k);
                        }
                        $qb->setParameter('name' . $k, $v);
                    }

                    $qb->andWhere($orCnd);

                } else {
                    if ($this->getTranslationCode() !== $defaultLanguage) {
                        $qb->andWhere('(CASE WHEN translations.name IS NOT NULL
                            THEN translations.name
                            ELSE default.name END) = :name');
                    } else {
                        $qb->andWhere('translations.name = :name');
                    }
                    $qb->setParameter('name', $name);
                }

                $result = $qb->getSingleResult();

                if ($result) {
                    $completeResult[$name] = $result;
                }

            } while ($result && $path);
        }

        return [$completeResult, $result];
    }

    /**
     * Find all by name part
     *
     * @param string $namePart Part of the category name
     * @param int $page Page number
     * @param int $countPerPage Number of elements per page
     * @param int $excludeCategoryId Id of the category which must be excluded
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function findAllByNamePart(string $namePart, int $page, int $countPerPage, int $excludeCategoryId)
    {
        $qb = $this->createQueryBuilder();

        $qb->linkLeft(
                $this->getMainAlias($qb) . '.translations',
                'translationsAll'
            );

        $qb->andWhere('translationsAll.name LIKE :namePart');

        if ($excludeCategoryId) {
            $excludeCategory = $this->getCategory($excludeCategoryId);
            $exRpos = $excludeCategory->getRpos();
            $exLpos = $excludeCategory->getLpos();

            $qb->andWhere('NOT (c.lpos >= :exLpos AND c.rpos <= :exRpos)');

            $qb->setParameter('exLpos', $exLpos);
            $qb->setParameter('exRpos', $exRpos);
        }

        $qb->setParameter('namePart', '%'.$namePart.'%');
        $qb->orderBy('c.lpos', 'ASC');
        $qb->setFirstResult($countPerPage * ($page - 1));
        $qb->setMaxResults($countPerPage);

        $paginator = new Paginator($qb, $fetchJoinCollection = false);

        return $paginator;
    }

    /**
     * Get plain list for tree
     *
     * @param integer $categoryId Category id OPTIONAL
     *
     * @param null    $excludeId
     *
     * @return array
     */
    public function getPlanListForTree($categoryId = null, $excludeId = null)
    {
        $categoryId = $categoryId ?: $this->getRootCategoryId();

        $excludeCat = !empty($excludeId) ? $this->find($excludeId) : null;

        if ($excludeCat) {
            $exRpos = $excludeCat->getRpos();
            $exLpos = $excludeCat->getLpos();
        }

        $list = [];
        foreach ($this->getChildsPlainListForTree($categoryId) as $category) {
            if ($excludeCat && $category['lpos'] >= $exLpos && $category['rpos'] <= $exRpos) {
                // Category should be excluded
                continue;

            } else {
                // Add category to the result
                $list[] = [
                    'category_id'  => $category['category_id'],
                    'depth'        => $category['depth'],
                    'translations' => $category['translations'],
                ];
                if ($category['rpos'] > $category['lpos'] + 1) {
                    $list = array_merge($list, $this->getPlanListForTree($category['category_id'], $excludeId));
                }
            }
        }

        return $list;
    }

    /**
     * Get children plain list for tree
     *
     * @param integer $categoryId Category id
     *
     * @return array
     */
    public function getChildsPlainListForTree($categoryId)
    {
        return $this->defineChildsPlainListForTreeQuery($categoryId)->getArrayResult();
    }

    /**
     * Add the conditions for the current subtree
     *
     * NOTE: function is public since it's needed to the Product model repository
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to modify
     * @param integer                    $categoryId   Current category ID
     * @param string                     $field        Name of the field to use OPTIONAL
     * @param integer                    $lpos         Left position OPTIONAL
     * @param integer                    $rpos         Right position OPTIONAL
     *
     * @return boolean
     */
    public function addSubTreeCondition(
        \Doctrine\ORM\QueryBuilder $queryBuilder,
        $categoryId,
        $field = 'lpos',
        $lpos = null,
        $rpos = null
    )
    {
        $category = $this->getCategory($categoryId);

        if ($category) {
            $index = static::$subTreeConditionIndex++;
            $lposName = 'sub_tree_condition_lpos_' . $index;
            $rposName = 'sub_tree_condition_rpos_' . $index;

            $queryBuilder->andWhere($queryBuilder->expr()->between('c.' . $field, ':' . $lposName, ':' . $rposName))
                ->setParameter($lposName, $lpos ?: $category->getLpos())
                ->setParameter($rposName, $rpos ?: $category->getRpos());
        }

        return isset($category);
    }


    /**
     * Define query for getChildsPlainListForTree()
     *
     * @param integer $categoryId Category id
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineChildsPlainListForTreeQuery($categoryId)
    {
        return $this->createPureQueryBuilder()
            ->select('c')
            ->addSelect('translations')
            ->linkInner('c.translations')
            ->linkInner('c.parent')
            ->andWhere('parent.category_id = :cid')
            ->setParameter('cid', $categoryId)
            ->orderBy('c.pos');
    }

    /**
     * Define the Doctrine query
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineMaxRightPosQuery()
    {
        return $this->createPureQueryBuilder()
            ->select('MAX(c.rpos)')
            ->setMaxResults(1);
    }

    /**
     * Define the Doctrine query
     *
     * @param integer $categoryId Category Id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFullTreeQuery($categoryId)
    {
        $queryBuilder = $this->createQueryBuilder()
            ->addSelect('translations');

        $this->addSubTreeCondition($queryBuilder, $categoryId ?: $this->getRootCategoryId());

        return $queryBuilder;
    }

    /**
     * Get categories plain list (child)
     *
     * @param integer $categoryId Category id
     *
     * @return array
     */
    protected function getCategoriesPlainListChild($categoryId)
    {
        $list = [];

        foreach ($this->defineSubcategoriesQuery($categoryId)->getArrayResult() as $category) {
            $list[] = $category;
            if ($category['rpos'] > $category['lpos'] + 1) {
                $list = array_merge($list, $this->getCategoriesPlainListChild($category['category_id']));
            }
        }

        return $list;
    }

    /**
     * Define the Doctrine query
     *
     * @param integer $categoryId Category Id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineSubcategoriesQuery($categoryId)
    {
        $queryBuilder = $this->initializeQueryBuilder($this->createPureQueryBuilder());

        if ($categoryId) {
            $queryBuilder->innerJoin('c.parent', 'cparent')
                ->andWhere('cparent.category_id = :parentId')
                ->setParameter('parentId', $categoryId);

        } else {
            $queryBuilder->andWhere('c.parent IS NULL');
        }

        return $queryBuilder;
    }

    /**
     * Define the Doctrine query
     *
     * @param \XLite\Model\Category $category Category
     * @param boolean               $hasSelf  Flag to include itself OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineSiblingsQuery(\XLite\Model\Category $category, $hasSelf = false)
    {
        $result = $this->defineSubcategoriesQuery($category->getParentId());

        if (!$hasSelf) {
            $result->andWhere('c.category_id <> :category_id')
                ->setParameter('category_id', $category->getCategoryId());
        }

        return $result;
    }

    /**
     * Define the Doctrine query
     *
     * @param \XLite\Model\Category $category   Category
     * @param integer               $maxResults Max results
     * @param boolean               $hasSelf    Flag to include itself OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineSiblingsFramedQuery(\XLite\Model\Category $category, $maxResults, $hasSelf = false)
    {
        $result = $this->defineSiblingsQuery($category, $hasSelf);
        $result->setMaxResults($maxResults);

        return $result;
    }


    /**
     * Define the Doctrine query
     *
     * @param integer $categoryId Category Id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineSubtreeQuery($categoryId)
    {
        return $this->defineFullTreeQuery($categoryId)
            ->andWhere('c.category_id <> :category_id')
            ->setParameter('category_id', $categoryId);
    }

    /**
     * Define the Doctrine query
     *
     * @param integer $categoryId Category Id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineCategoryPathQuery($categoryId)
    {
        $queryBuilder = $this->createQueryBuilder();
        $category = $this->getCategory($categoryId);

        if ($category) {
            $this->addSubTreeCondition($queryBuilder, $categoryId, 'lpos', 1, $category->getLpos());

            $this->addSubTreeCondition(
                $queryBuilder,
                $categoryId,
                'rpos',
                $category->getRpos(),
                $this->getMaxRightPos()
            );

            $queryBuilder->orderBy('c.lpos', 'ASC');

        } else {
            // :TODO: - throw exception
        }

        return $queryBuilder;
    }

    /**
     * Define the Doctrine query
     *
     * @param integer $categoryId Category Id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineCategoryDepthQuery($categoryId)
    {
        return $this->defineCategoryPathQuery($categoryId)
            ->select('COUNT(c.category_id) - 1')
            ->setMaxResults(1);
    }

    /**
     * Define the Doctrine query
     *
     * @param integer $productId Product Id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineSearchByProductIdQuery($productId)
    {
        return $this->createQueryBuilder()
            ->innerJoin('c.categoryProducts', 'cp')
            ->innerJoin('cp.product', 'product')
            ->andWhere('product.product_id = :productId')
            ->setParameter('productId', $productId)
            ->addOrderBy('cp.orderby', 'ASC');
    }

    /**
     * Define the Doctrine query
     *
     * @param string  $index        Field name
     * @param integer $relatedIndex Related index value
     * @param integer $offset       Increment OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineUpdateIndexQuery($index, $relatedIndex, $offset = 2)
    {
        $expr = new \Doctrine\ORM\Query\Expr();

        return $this->createPureQueryBuilder('c')
            ->update($this->_entityName, 'c')
            ->set('c.' . $index, 'c.' . $index . ' + :offset')
            ->andWhere($expr->gt('c.' . $index, ':relatedIndex'))
            ->setParameter('offset', $offset)
            ->setParameter('relatedIndex', $relatedIndex);
    }

    /**
     * Adds additional condition to the query for checking if category is enabled
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param string                     $alias        Entity alias OPTIONAL
     *
     * @return void
     */
    protected function addEnabledCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        if ($this->getEnabledCondition()) {
            $queryBuilder->andWhere(($alias ?: $this->getDefaultAlias()) . '.enabled = :enabled')
                ->setParameter('enabled', true);
        }
    }

    /**
     * Adds additional condition to the query for checking if category is enabled
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param string                     $alias        Entity alias OPTIONAL
     *
     * @return void
     */
    protected function addMembershipCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        if ($this->getMembershipCondition()) {
            $alias = $alias ?: $this->getDefaultAlias();
            $membership = \XLite\Core\Auth::getInstance()->getMembershipId();

            if ($membership) {
                $queryBuilder->leftJoin($alias . '.memberships', 'membership')
                    ->andWhere('membership.membership_id = :membershipId OR membership.membership_id IS NULL')
                    ->setParameter('membershipId', \XLite\Core\Auth::getInstance()->getMembershipId());

            } else {
                $queryBuilder->leftJoin($alias . '.memberships', 'membership')
                    ->andWhere('membership.membership_id IS NULL');
            }
        }
    }

    /**
     * Adds additional condition to the query to order categories
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param string                     $alias        Entity alias OPTIONAL
     *
     * @return void
     */
    protected function addOrderByCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        $queryBuilder
            // We need POS ordering since POS and LPOS are orderings inside the same one level.
            // LPOS is formed by the system (by adding into the level)
            // POS  is formed manually by admin and must have priority
            ->addOrderBy(($alias ?: $this->getDefaultAlias()) . '.pos', 'ASC')
            ->addOrderBy(($alias ?: $this->getDefaultAlias()) . '.category_id', 'ASC');
    }

    /**
     * Adds additional condition to the query to order categories
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param int                        $value        Category id to exclude
     * @param string                     $alias        Entity alias OPTIONAL
     *
     * @return void
     */
    protected function addExcludeCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $alias = null)
    {
        $alias = $alias ?: $this->getDefaultAlias();

        $queryBuilder->andWhere($alias . '.category_id <> :rootId')
            ->setParameter('rootId', (int)$value);
    }

    /**
     * Adds additional condition to the query to order categories
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder object
     * @param string                     $alias        Entity alias OPTIONAL
     *
     * @return void
     */
    protected function addExcludeRootCondition(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        $this->addExcludeCondition($queryBuilder, $this->getRootCategoryId(), $alias);
    }

    /**
     * Return maximum index in the "nested set" tree
     *
     * @return integer
     */
    protected function getMaxRightPos()
    {
        if (!isset($this->maxRightPos)) {
            $this->maxRightPos = $this->defineMaxRightPosQuery()->getSingleScalarResult();
        }

        return $this->maxRightPos;
    }

    /**
     * Prepare data for a new category node
     *
     * @param \XLite\Model\Category $entity Category object
     * @param \XLite\Model\Category $parent Parent category object OPTIONAL
     *
     * @return void
     */
    protected function prepareNewCategoryData(\XLite\Model\Category $entity, \XLite\Model\Category $parent = null)
    {
        if (!isset($parent)) {
            $parent = $this->getCategory($entity->getParentId());
        }

        if (isset($parent)) {
            $entity->setLpos($parent->getLpos() + 1);
            $entity->setRpos($parent->getLpos() + 2);
            $entity->setDepth($parent->getDepth() + 1);

        } else {
            // :TODO: - rework - add support last root category
            $entity->setLpos(1);
            $entity->setRpos(2);
        }

        $entity->setParent($parent);
    }

    /**
     * Prepare data for a the "updateQuickFlags()" method
     *
     * @param integer $scAll     The "subcategories_count_all" flag value
     * @param integer $scEnabled The "subcategories_count_enabled" flag value
     *
     * @return array
     */
    protected function prepareQuickFlags($scAll, $scEnabled)
    {
        return [
            'subcategories_count_all'     => $scAll,
            'subcategories_count_enabled' => $scEnabled,
        ];
    }

    /**
     * Prepare passed ID
     * NOTE: see E:0038835 (external BT)
     *
     * @param mixed $categoryId Category ID
     *
     * @return integer|void
     */
    protected function prepareCategoryId($categoryId)
    {
        return abs((int)$categoryId) ?: null;
    }

    /**
     * Update quick flags for a category
     *
     * @param \XLite\Model\Category $entity Category
     * @param array                 $flags  Flags to set
     *
     * @return void
     */
    protected function updateQuickFlags(\XLite\Model\Category $entity, array $flags)
    {
        $quickFlags = $entity->getQuickFlags();

        if (!isset($quickFlags)) {
            $quickFlags = new \XLite\Model\Category\QuickFlags();
            $quickFlags->setCategory($entity);
            $entity->setQuickFlags($quickFlags);
        }

        foreach ($flags as $name => $delta) {
            $name = \Includes\Utils\Converter::convertToPascalCase($name);
            $quickFlags->{'set' . $name}($quickFlags->{'get' . $name}() + $delta);
        }
    }

    // {{{ Methods to manage entities

    /**
     * Remove all subcategories
     *
     * @param integer $categoryId Main category
     *
     * @return void
     */
    public function deleteSubcategories($categoryId)
    {
        $this->deleteInBatch($this->getSubtree($categoryId));
    }

    /**
     * Insert single entity
     *
     * @param \XLite\Model\Category|array $entity Data to insert OPTIONAL
     *
     * @return \XLite\Model\Category
     */
    protected function performInsert($entity = null)
    {
        /** @var \XLite\Model\Category $entity */
        $entity = parent::performInsert($entity);
        $parentID = $entity->getParentId();

        if (empty($parentID)) {
            // Insert root category
            $this->prepareNewCategoryData($entity);

        } else {
            // Get parent for non-root category
            $parent = $this->getCategory($parentID);

            if ($parent) {
                // Reload parent category from database to get correct value of indexes
                // after batch update in previous call of performInsert on import
                \XLite\Core\Database::getEM()->merge($parent);

                // Update indexes in the nested set
                //$this->defineUpdateIndexQuery('lpos', $parent->getLpos())->execute();
                //$this->defineUpdateIndexQuery('rpos', $parent->getLpos())->execute();

                // Create record in DB
                $this->prepareNewCategoryData($entity, $parent);

            } else {
                \Includes\ErrorHandler::fireError(__METHOD__ . ': category #' . $parentID . ' not found');
            }
        }

        // Update quick flags
        if (isset($parent) && null == $entity->getCategoryId()) {
            $this->updateQuickFlags($parent, $this->prepareQuickFlags(1, $entity->getEnabled() ? 1 : -1));
        }

        return $entity;
    }

    /**
     * Update single entity
     *
     * @param \XLite\Model\AEntity $entity Entity to use
     * @param array                $data   Data to save OPTIONAL
     *
     * @return void
     */
    protected function performUpdate(\XLite\Model\AEntity $entity, array $data = [])
    {
        if (!empty($data)) {
            $changeset = [
                'enabled' => [
                    $entity->getEnabled(),
                    isset($data['enabled']) ? $data['enabled'] : null
                ]
            ];

        } else {
            $uow = \XLite\Core\Database::getEM()->getUnitOfWork();
            $uow->computeChangeSets();
            $changeset = $uow->getEntityChangeSet($entity);
        }

        if (!$changeset && $entity->_getPreviousState()->enabled !== null) {
            $changeset = [
                'enabled' => [
                    (bool)$entity->_getPreviousState()->enabled,
                    $entity->getEnabled(),
                ]
            ];
        }

        if (isset($changeset['enabled'][0], $changeset['enabled'][1])
            && $entity->getParent()
            && ($changeset['enabled'][0] xor ((bool)$changeset['enabled'][1]))
        ) {
            $this->updateQuickFlags(
                $entity->getParent(),
                $this->prepareQuickFlags(0, ($changeset['enabled'][0] ? -1 : 1))
            );
        }

        parent::performUpdate($entity, $data);
    }

    /**
     * Delete single entity
     *
     * @param \XLite\Model\AEntity $entity Entity to detach
     *
     * @return void
     */
    protected function performDelete(\XLite\Model\AEntity $entity)
    {
        // Update quick flags
        if ($entity->getParent()) {
            $this->updateQuickFlags($entity->getParent(), $this->prepareQuickFlags(-1, $entity->getEnabled() ? -1 : 0));
        }

        // Root category cannot be removed. Only its subtree
        $onlySubtree = ($entity->getCategoryId() == $this->getRootCategoryId());

        // Calculate some variables
        $right = $entity->getRpos() - ($onlySubtree ? 1 : 0);
        $width = $entity->getRpos() - $entity->getLpos() - ($onlySubtree ? 1 : -1);

        // Update indexes in the nested set.
        // FIXME: must not use execute()
        $this->defineUpdateIndexQuery('lpos', $right, -$width)->execute();
        $this->defineUpdateIndexQuery('rpos', $right, -$width)->execute();

        if ($onlySubtree) {
            $this->deleteInBatch($this->getSubtree($entity->getCategoryId()), false);

        } else {
            parent::performDelete($entity);
        }
    }

    // }}}

    /**
     * Load raw fixture
     *
     * @param \XLite\Model\AEntity $entity  Entity
     * @param array                $record  Record
     * @param array                $regular Regular fields info OPTIONAL
     * @param array                $assocs  Associations info OPTIONAL
     *
     * @return void
     */
    public function loadRawFixture(
        \XLite\Model\AEntity $entity,
        array $record,
        array $regular = [],
        array $assocs = []
    )
    {
        /** @var \XLite\Model\Category $entity */
        if ($entity->isPersistent() && $this->find($entity->getCategoryId())) {
            $this->performUpdate($entity);

        } else {
            $this->performInsert($entity);
        }

        parent::loadRawFixture($entity, $record, $regular, $assocs);
    }

    /**
     * Assemble associations from record
     *
     * @param array $record Record
     * @param array $assocs Associations info OPTIONAL
     *
     * @return array
     */
    protected function assembleAssociationsFromRecord(array $record, array $assocs = [])
    {
        if (!isset($record['quickFlags'])) {
            $record['quickFlags'] = [];
        }

        return parent::assembleAssociationsFromRecord($record, $assocs);
    }

    // {{{ Export routines

    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForExportQuery()
    {
        $qb = parent::defineCountForExportQuery();
        $this->addSubTreeCondition($qb, $this->getRootCategoryId());

        return $qb;
    }

    /**
     * Prepare certain search condition
     *
     * @Api\Condition(description="Filters categories by parent id", type="integer")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndParent(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value && !is_object($value)) {
            $value = \XLite\Core\Database::getRepo('XLite\Model\Category')->find((int)$value);
        }

        if ($value) {
            $queryBuilder->andWhere('c.parent = :parent')
                ->setParameter('parent', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @Api\Condition(description="Retrieve categories subtree by parent id", type="integer")
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Parent category id
     *
     * @return void
     */
    protected function prepareCndSubTree(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $this->addExcludeCondition($queryBuilder, $value);
            $this->addSubTreeCondition($queryBuilder, $value);
        }
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
        $queryBuilder = parent::defineExportIteratorQueryBuilder($position);

        $this->addSubTreeCondition($queryBuilder, $this->getRootCategoryId());

        return $queryBuilder;
    }

    // }}}

    // {{{ Correct categories structure methods

    /**
     * Correct categories structure: lpos, rpos and depth fields
     *
     * @return void
     */
    public function correctCategoriesStructure()
    {
        $nestedSetCorrector = new \XLite\Logic\NestedSet(
            $this->getCategoriesRawData()
        );

        try {
            list($data, $quickFlags) = $nestedSetCorrector->recalculateStructure();
        } catch (\Exception $exception) {
            \XLite\Logger::getInstance()->log(
                'Something is wrong categories in nestedSet recalculation: ' . $exception->getMessage(),
                LOG_ERR
            );
            return;
        }

        if ($data) {
            foreach ($data as $catId => $d) {
                $query = 'UPDATE ' . $this->getTableName()
                         . ' SET ' . implode(', ', array_map(function ($v) {
                        return $v . ' = ?';
                    }, array_keys($d)))
                         . ' WHERE category_id = ?';
                $d[] = $catId;
                \XLite\Core\Database::getEM()->getConnection()->executeUpdate($query, array_values($d));
            }
        }

        if ($quickFlags) {
            $qfKeys = [
                'category_id',
                'subcategories_count_all',
                'subcategories_count_enabled',
            ];

            foreach ($quickFlags as $qfData) {
                $qfQuery = 'REPLACE INTO '
                           . \XLite\Core\Database::getRepo('XLite\Model\Category\QuickFlags')->getTableName()
                           . ' (' . implode(', ', $qfKeys) . ')'
                           . ' VALUES (' . implode(', ', array_fill(0, count($qfData), '?')) . ')';
                \XLite\Core\Database::getEM()->getConnection()->executeUpdate($qfQuery, array_values($qfData));
            }
        }
    }

    /**
     * Simplified search for categories data
     *
     * @return array
     */
    protected function getCategoriesRawData()
    {
        $fields = [
            'c.category_id as id',
            'c.parent_id',
            'c.lpos',
            'c.rpos',
            'c.depth',
            'c.pos',
            'c.enabled',
            'qf.subcategories_count_all         as subnodes_count_all',
            'qf.subcategories_count_enabled     as subnodes_count_enabled',
        ];

        $query = 'SELECT ' . implode(',', $fields) . ' FROM ' . $this->getTableName() . ' c '
                 . ' LEFT JOIN ' . \XLite\Core\Database::getRepo('XLite\Model\Category\QuickFlags')->getTableName()
                 . ' qf ON c.category_id = qf.category_id '
                 . ' ORDER BY c.pos';

        return \Includes\Utils\Database::fetchAll($query);
    }

    // }}}

    // {{{ Product in category

    /**
     * Check if product present in category
     *
     * @param \XLite\Model\Category|integer $category Category
     * @param \XLite\Model\Product|integer  $product  Product
     *
     * @return boolean
     */
    public function hasProduct($category, $product)
    {
        return (bool)$this->defineHasProduct($category, $product)->getSingleScalarResult();
    }

    /**
     * Define the Doctrine query
     *
     * @param \XLite\Model\Category|integer $category Category
     * @param \XLite\Model\Product|integer  $product  Product
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineHasProduct($category, $product)
    {
        return $this->createQueryBuilder()
            ->andWhere('c.category_id = :categoryId')
            ->setParameter('categoryId', is_object($category) ? $category->getCategoryId() : $category)
            ->innerJoin('c.categoryProducts', 'cp')
            ->andWhere('cp.product = :product')
            ->setParameter('product', $product)
            ->selectCount();
    }

    // }}}

    // {{{

    /**
     * Get categories as dtos with runtime cache
     *
     * @return array
     */
    public function getAllCategoriesAsDTO()
    {
        $cacheParameters = [
            'allCategoriesDTOs',
            'repo',
            \XLite\Core\Session::getInstance()->getLanguage()
                ? \XLite\Core\Session::getInstance()->getLanguage()->getCode()
                : '',
            \XLite\Core\Database::getRepo('XLite\Model\Category')->getVersion(),
        ];

        return \XLite\Core\Cache\ExecuteCached::executeCached(function () {
            $rawCategories = $this->getAllCategoriesAsDTOQueryBuilder()->getResult();

            return $this->categoriesWithPathdata($rawCategories);
        }, $cacheParameters);
    }

    /**
     * @param $rawCategories
     *
     * @return array
     */
    protected function categoriesWithPathdata($rawCategories)
    {
        if (!$rawCategories || ($rawCategories instanceof Collection && !$rawCategories->count())) {
            return [];
        }

        $rawCategories = $this->sortCategoriesTree($rawCategories);

        $categories = [];
        foreach ($rawCategories as $category) {
            if (!$category['name']) {
                $category['name'] = $this->getFirstTranslatedName($category['id']);
            }

            $categories[$category['id']] = $category;
        }

        $rootId = $this->getRootCategoryId();
        $separator = ' / ';

        array_walk($categories, function (array &$category) use ($categories, $rootId, $separator) {
            $result = [$category['name']];
            $idsPath = [$category['id']];
            $parentId = (int)$category['parent_id'];
            while ($parentId !== $rootId) {
                $found = false;
                foreach ($categories as $tmpCategory) {
                    if ((int)$tmpCategory['id'] === $parentId) {
                        $parentId = (int)$tmpCategory['parent_id'];
                        $idsPath[] = $tmpCategory['id'];
                        $result[] = $tmpCategory['name'];

                        if ($category['accessible'] && !$tmpCategory['accessible']) {
                            $category['accessible'] = false;
                        }


                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    break;
                }
            }

            $parts = array_reverse($result);
            $path = array_slice($parts, 0, -1);

            $category['fullName'] = implode($separator, $parts);

            if (count($path) > 0) {
                $category['fullNameHtml'] = '<span class="path">' . implode($separator, $path) . $separator . '</span><span class="name">' . array_pop($parts) . '</span>';
            } else {
                $category['fullNameHtml'] = '<span class="name">' . implode($separator, $parts) . '</span>';
            }

            $category['idsPath'] = array_reverse($idsPath);
        });

        return $categories;
    }

    /**
     * @param array $list
     *
     * @return array
     */
    protected function sortCategoriesTree($list)
    {
        $result = [];

        if (!empty($list)) {
            $collected = [];

            do {
                $count = count($result);
                if ($result) {
                    $tmpResult = [];
                    foreach ($result as $key => $category) {
                        $tmpResult[] = $category;
                        if (!isset($collected[$category['id']])) {
                            $tmpResult = array_merge($tmpResult, $this->sortCategoriesTreeForParent($list, $category['id']));
                            $collected[$category['id']] = true;
                        }
                    }
                    $result = $tmpResult;
                } else {
                    $result = $this->sortCategoriesTreeForParent($list);
                }
            } while ($count !== count($result));
        }

        return $result;
    }

    /**
     * @param      $list
     * @param null $identity
     *
     * @return array
     */
    protected function sortCategoriesTreeForParent($list, $identity = null)
    {
        $categories = [];

        foreach ($list as $category) {
            if ($identity && $category['parent_id'] == $identity) {
                $categories[] = $category;
            } else if (!$identity && $category['depth'] == 0) {
                $categories[] = $category;
            }
        }

        usort($categories, function ($cat1, $cat2) {
            $pos1 = $cat1['pos'];
            $pos2 = $cat2['pos'];

            if ($pos1 > $pos2) {
                return 1;
            } else {
                return $pos1 === $pos2 ? 0 : -1;
            }
        });

        return $categories;
    }

    /**
     * Get categories as dtos queryBuilder
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function getAllCategoriesAsDTOQueryBuilder()
    {
        $queryBuilder = parent::createQueryBuilder();

        $this->addOrderByCondition($queryBuilder, 'c');
        $this->addExcludeRootCondition($queryBuilder, 'c');

        $queryBuilder->select('c.category_id as id');

        if ($this->getTranslationCode() !== \XLite::getDefaultLanguage()) {
            // Add additional join to translations with default language code
            $this->addDefaultTranslationJoins(
                $queryBuilder,
                $this->getMainAlias($queryBuilder),
                'defaults',
                \XLite::getDefaultLanguage()
            );
            $queryBuilder->addSelect('(CASE WHEN translations.name IS NOT NULL THEN translations.name ELSE defaults.name END) as name');
        } else {
            $queryBuilder->addSelect('translations.name');
        }

        $queryBuilder->addSelect('IDENTITY(c.parent) as parent_id');
        $queryBuilder->addSelect('c.depth as depth');
        $queryBuilder->addSelect('c.pos as pos');
        $queryBuilder->addSelect('c.enabled as accessible');

        $queryBuilder->linkLeft('c.children', 'conditional_children');

        $queryBuilder->linkLeft(
            'c.cleanURLs',
            'cleanURLs',
            'WITH',
            'cleanURLs.id = (SELECT MAX(cl.id) FROM XLite\Model\CleanURL cl WHERE cl.category = c.category_id)'
        );
        $queryBuilder->addSelect('cleanURLs.cleanURL as cleanURL');

        $queryBuilder->addGroupBy('c.category_id');
        $queryBuilder->orderBy('c.lpos');

        return $queryBuilder;
    }

    // }}}

    /**
     * Get categories as dtos, filtered by some term (using runtime cache)
     *
     * @param string $term
     *
     * @return array
     */
    public function getFilteredCategoriesAsDTO($term)
    {
        return $this->executeCachedRuntime(function () use ($term) {
            $categories = $this->getAllCategoriesAsDTO();

            return array_filter($categories, function($item) use ($term) {
                return stripos($item['fullName'], $term) !== false;
            });
        });
    }

    /**
     * Get categories as dtos with runtime cache
     *
     * @return array
     */
    public function getCategoriesAsDTO()
    {
        return $this->executeCachedRuntime(function () {
            return $this->getCategoriesAsDTOQueryBuilder()->getResult();
        });
    }

    /**
     * Get categories as dtos queryBuilder
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function getCategoriesAsDTOQueryBuilder()
    {
        $queryBuilder = $this->createQueryBuilder();

        $queryBuilder->select('c.category_id as id');

        if ($this->getTranslationCode() !== \XLite::getDefaultLanguage()) {
            // Add additional join to translations with default language code
            $this->addDefaultTranslationJoins(
                $queryBuilder,
                $this->getMainAlias($queryBuilder),
                'defaults',
                \XLite::getDefaultLanguage()
            );
            $queryBuilder->addSelect('(CASE WHEN translations.name IS NOT NULL THEN translations.name ELSE defaults.name END) as name');
        } else {
            $queryBuilder->addSelect('translations.name');
        }

        $queryBuilder->addSelect('IDENTITY(c.parent) as parent_id');
        $queryBuilder->addSelect('c.depth as depth');
        $queryBuilder->addSelect('count(conditional_children) as subcategoriesCount');

        $queryBuilder->linkLeft('c.children', 'conditional_children',
            'WITH',
            'conditional_children.enabled = true AND (:membership MEMBER OF conditional_children.memberships OR conditional_children.memberships IS EMPTY)'
        );

        $queryBuilder->setParameter('membership', \XLite\Core\Auth::getInstance()->getMembershipId());

        if (LC_USE_CLEAN_URLS) {
            $queryBuilder->linkLeft(
                'c.cleanURLs',
                'cleanURLs',
                'WITH',
                'cleanURLs.id = (SELECT MAX(cl.id) FROM XLite\Model\CleanURL cl WHERE cl.category = c.category_id)'
            );
            $queryBuilder->addSelect('cleanURLs.cleanURL as cleanURL');
        }

        $queryBuilder->addGroupBy('c.category_id');

        return $queryBuilder;
    }

    /**
     * @param  array $category current category
     * @param  array $params
     *
     * @return string
     */
    public function getUrlByDTO($category, $params = [])
    {
        $defaultFormat = \XLite\Core\Converter::buildURL('category', '', ['category_id' => $category['id']], null, false, false);

        if (!LC_USE_CLEAN_URLS || empty($category['cleanURL'])) {
            return $defaultFormat;
        }

        $result   = [];
        $dtos     = $this->getCategoriesAsDTO();
        $result[] = $category['cleanURL'];

        while ($category['depth'] > 0) {
            $parent = array_search($category['parent_id'], array_column($dtos, 'id'));

            if (false === $parent) {
                return '';
            }

            $parent = $dtos[$parent];

            if (empty($parent['cleanURL'])) {
                return $defaultFormat;
            }

            $result[] = $parent['cleanURL'];
            $category = $parent;
        }

        $result = \XLite\Core\Database::getRepo('XLite\Model\CleanURL')->buildUrlByData('category', $result, $params);

        return $result;
    }

    /**
     * Prepare certain search condition
     *
     * @Api\Condition(description="Search by term", type="string")
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                                   $value        Condition data
     *
     * @return void
     */
    protected function prepareCndTerm(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value)
    {
        /** @var \XLite\Model\QueryBuilder\AQueryBuilder $qb */
        $qb = $queryBuilder ?: $this->searchState['queryBuilder'];

        if ($value) {
            if ($this->getTranslationCode() !== \XLite::getDefaultLanguage()) {
                // Add additional join to translations with default language code
                $this->addDefaultTranslationJoins(
                    $qb,
                    $this->getMainAlias($qb),
                    'defaults',
                    \XLite::getDefaultLanguage()
                );

                $qb->andWhere(
                    $qb->expr()->orX(
                        'translations.name LIKE :searchTerm',
                        'translations.name IS NULL AND defaults.name LIKE :searchTerm'
                    )
                )
                    ->setParameter('searchTerm', '%' . (string)$value . '%');

                $qb->addSelect('if(locate(\'' . $value . '\', translations.name)=1,0,1) termLocate');

            } else {
                $qb->andWhere($qb->expr()->like('translations.name', ':searchTerm'))
                    ->setParameter('searchTerm', '%' . (string)$value . '%');

                $qb->addSelect('if(locate(\'' . $value . '\', translations.name)=1,0,1) termLocate');
            }
        }
    }

    /**
     * Get translation
     *
     * @param integer $categoryId Category id
     *
     * @return string
     */
    public function getFirstTranslatedName($categoryId)
    {
        $result = $this->createPureQueryBuilder()
            ->select('translations.name')
            ->linkLeft('c.translations')
            ->where('translations.name IS NOT NULL')
            ->andWhere('c.category_id = :category_id')
            ->setParameter('category_id', $categoryId)
            ->getSingleScalarResult();

        return is_string($result)
            ? $result
            : '';
    }

    /**
     * Define remove data iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineRemoveDataQueryBuilder($position)
    {
        $qb = $this->createPureQueryBuilder();
        $qb->andWhere($this->getMainAlias($qb) . '.depth = 0');

        return $qb;
    }

    /**
     * Check if given categories contains internal category products
     *
     * @param array $identifiers Category identifiers
     *
     * @return boolean
     */
    public function checkForInternalCategoryProducts($identifiers)
    {
        if (!empty($identifiers) && is_array($identifiers)) {
            $tablePrefix = \XLite::getInstance()->getOptions(['database_details', 'table_prefix']);
            $productsTable = $tablePrefix . 'products';
            $categoriesTable = $tablePrefix . 'categories';
            $categoryProductsTable = $tablePrefix . 'category_products';

            $categoriesQuery =
                "SELECT ct.category_id "
                . "FROM  {$categoriesTable} c "
                . "LEFT JOIN  {$categoriesTable} ct "
                . "ON ct.lpos >= c.lpos AND ct.rpos <= c.rpos "
                . "WHERE c.category_id IN (:identifiers)";

            $internalCategoryProductsQuery =
                "SELECT p.product_id "
                . "FROM {$productsTable} p "
                . "INNER JOIN {$categoryProductsTable} cpt "
                . "ON cpt.product_id = p.product_id "
                . "INNER JOIN {$categoriesTable} ct "
                . "ON cpt.category_id = ct.category_id AND ct.category_id IN ($categoriesQuery) "
                . "GROUP BY p.product_id";

            $externalCategoryProductsQuery =
                "SELECT p.product_id "
                . "FROM {$productsTable} p "
                . "INNER JOIN {$categoryProductsTable} cpt "
                . "ON cpt.product_id = p.product_id "
                . "INNER JOIN {$categoriesTable} ct "
                . "ON cpt.category_id = ct.category_id AND ct.category_id IN ($categoriesQuery) "
                . "INNER JOIN {$categoryProductsTable} acpt "
                . "ON acpt.product_id = p.product_id "
                . "INNER JOIN {$categoriesTable} act "
                . "ON acpt.category_id = act.category_id AND act.category_id NOT IN ($categoriesQuery) "
                . "GROUP BY p.product_id";

            $internalStmt = \XLite\Core\Database::getEM()->getConnection()->executeQuery(
                $internalCategoryProductsQuery,
                ['identifiers' => $identifiers],
                ['identifiers' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]
            );

            $externalStmt = \XLite\Core\Database::getEM()->getConnection()->executeQuery(
                $externalCategoryProductsQuery,
                ['identifiers' => $identifiers],
                ['identifiers' => \Doctrine\DBAL\Connection::PARAM_INT_ARRAY]
            );

            return $internalStmt->rowCount() > $externalStmt->rowCount();
        }

        return false;
    }
}
