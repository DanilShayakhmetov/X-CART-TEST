<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Clean URL repository
 *
 * @Api\Operation\Create(modelClass="XLite\Model\CleanURL", summary="Add new clean url")
 * @Api\Operation\Read(modelClass="XLite\Model\CleanURL", summary="Retrieve clean url by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\CleanURL", summary="Retrieve all clean urls")
 * @Api\Operation\Update(modelClass="XLite\Model\CleanURL", summary="Update clean url by id")
 * @Api\Operation\Delete(modelClass="XLite\Model\CleanURL", summary="Delete clean url by id")
 */
class CleanURL extends \XLite\Model\Repo\ARepo
{
    use ExecuteCachedTrait;

    const CATEGORY_URL_FORMAT_NON_CANONICAL_NO_EXT = 'domain/parent/goalcategory/';
    const CATEGORY_URL_FORMAT_CANONICAL_NO_EXT = 'domain/goalcategory/';
    const CATEGORY_URL_FORMAT_NON_CANONICAL_EXT = 'domain/parent/goalcategory.html';
    const CATEGORY_URL_FORMAT_CANONICAL_EXT = 'domain/goalcategory.html';

    const PRODUCT_URL_FORMAT_NO_EXT = 'domain/goalproduct';
    const PRODUCT_URL_FORMAT_EXT = 'domain/goalproduct.html';

    const PRODUCT_URL_CANONICAL_WITH_CATEGORY = 'domain/main_category_clean_url/product_clean_url';

    /**
     * Limit of iterations to generate clean URL
     */
    const CLEAN_URL_CHECK_LIMIT = 1000;

    /**
     * Use this char as separator, if the default one is not set in the config
     */
    const CLEAN_URL_DEFAULT_SEPARATOR = '-';

    /**
     * Default extension
     */
    const CLEAN_URL_DEFAULT_EXTENSION = 'html';

    const PLACEHOLDER = '#PLACEHOLDER#';

    /**
     * Returns available entities types
     *
     * @return array
     */
    public static function getEntityTypes()
    {
        return [
            'XLite\Model\Product' => 'product',
            'XLite\Model\Category' => 'category',
        ];
    }

    /**
     * Returns entity type
     *
     * @param \XLite\Model\AEntity|string $entity Entity
     *
     * @return string
     */
    public static function getEntityType($entity)
    {
        $types = static::getEntityTypes();
        $className = is_object($entity)
            ? \Doctrine\Common\Util\ClassUtils::getClass($entity)
            : \Doctrine\Common\Util\ClassUtils::getRealClass($entity);

        return isset($types[$className])
            ? $types[$className]
            : null;
    }

    // {{{ Check clean url

    /**
     * Check for format
     *
     * @param \XLite\Model\AEntity|string $entity Entity or class name
     *
     * @return boolean
     */
    public function getPattern($entity)
    {
        $entityType = static::getEntityType($entity);

        $method = $entityType
            ? __FUNCTION__ . \XLite\Core\Converter::convertToCamelCase($entityType)
            : null;

        return method_exists($this, $method)
            ? $this->{$method}($entity)
            : $this->getCommonPattern($entity);
    }

    /**
     * Returns common url regexp pattern
     *
     * @return string
     */
    protected function getCommonPattern()
    {
        return '[.\w_\-]*';
    }

    // }}}

    // {{{ URL Separator

    /**
     * Check for format
     *
     * @param \XLite\Model\AEntity|string $entity Entity or class name
     *
     * @return boolean
     */
    public function getSeparator($entity)
    {
        $entityType = static::getEntityType($entity);

        $method = $entityType
            ? __FUNCTION__ . \XLite\Core\Converter::convertToCamelCase($entityType)
            : null;

        $result = method_exists($this, $method)
            ? $this->{$method}($entity)
            : $this->getCommonSeparator();

        if (empty($result)
            || !preg_match('/' . $this->getPattern($entity) . '/S', $result)
        ) {
            $result = static::CLEAN_URL_DEFAULT_SEPARATOR;
        }

        return $result;
    }

    /**
     * Returns common separator
     *
     * @return string
     */
    public function getCommonSeparator()
    {
        return \Includes\Utils\ConfigParser::getOptions(['clean_urls', 'default_separator']);
    }

    // }}}

    // {{{ Capitalize flag

    /**
     * Return true if words included into clean URL should be capitalized
     *
     * @param \XLite\Model\AEntity|string $entity Entity or class name
     *
     * @return boolean
     */
    protected function isCapitalizeWords($entity)
    {
        $entityType = static::getEntityType($entity);

        $method = $entityType
            ? __FUNCTION__ . \XLite\Core\Converter::convertToCamelCase($entityType)
            : null;

        return method_exists($this, $method)
            ? $this->{$method}($entity)
            : $this->isCommonCapitalizeWords();
    }

    /**
     * Return true if words included into clean URL should be capitalized
     *
     * @return boolean
     */
    protected function isCommonCapitalizeWords()
    {
        return \Includes\Utils\ConfigParser::getOptions(['clean_urls', 'capitalize_words']);
    }

    // }}}

    // {{{ Generate clean url

    /**
     * Generate clean URL
     *
     * @param \XLite\Model\AEntity $entity         Entity
     * @param string               $base           Base OPTIONAL
     *
     * @return string
     */
    public function generateCleanURL(\XLite\Model\AEntity $entity, $base = null)
    {
        $result = '';

        if (null === $base) {
            $base = $this->getURLBase($entity);
        }

        if (!$this->isUseUnicode()) {
            $base = \XLite\Core\Converter::convertToTranslit($base);
        }
        $base = $this->processHTMLEntities($base);

        if ($base) {
            $separator = $this->getSeparator($entity);
            $result .= mb_strtolower(preg_replace('/\W+/Su', $separator, $base));

            if ($this->isCapitalizeWords($entity)) {
                $result = implode($separator, array_map(
                    function ($word) {
                        return mb_strtoupper(mb_substr($word, 0, 1)) . mb_substr($word, 1);
                    },
                    explode($separator, $result)
                ));
            }

            $suffix = '';
            $increment = 1;

            $result = mb_substr(
                $result,
                0,
                255 - mb_strlen($separator . (string) static::CLEAN_URL_CHECK_LIMIT . $this->postProcessURL('', $entity))
            );

            while (!$this->isURLUnique($this->postProcessURL($result . $suffix, $entity), $entity)
                && static::CLEAN_URL_CHECK_LIMIT > $increment
            ) {
                $suffix = $separator . $increment++;
            }

            if (!empty($suffix)) {
                $result .= $suffix;
            }

            $result = $this->postProcessURL($result, $entity);
        }

        return $result;
    }

    /**
     * Process HTML entities
     *
     * @param string $base
     *
     * @return string
     */
    protected function processHTMLEntities($base)
    {
        $entities = [
            '&' => ' and ',
        ];

        return str_replace(array_keys($entities), array_values($entities), $base);
    }

    /**
     * Returns common separator
     *
     * @return string
     */
    public function isUseUnicode()
    {
        return \Includes\Utils\ConfigParser::getOptions(['clean_urls', 'use_unicode']);
    }

    // }}}

    // {{{ URL Base

    /**
     * Returns clean url base value field name
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return string
     */
    public function getBaseFieldName($entity)
    {
        $entityType = static::getEntityType($entity);

        $method = $entityType
            ? __FUNCTION__ . \XLite\Core\Converter::convertToCamelCase($entityType)
            : null;

        return method_exists($this, $method)
            ? $this->{$method}($entity)
            : 'name';
    }

    /**
     * Returns clean url base value
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return string
     */
    protected function getURLBase($entity)
    {
        $entityType = static::getEntityType($entity);

        $method = $entityType
            ? __FUNCTION__ . \XLite\Core\Converter::convertToCamelCase($entityType)
            : null;

        $result = method_exists($this, $method)
            ? $this->{$method}($entity)
            : null;

        return null === $result
            ? $entity->{$this->getBaseFieldName($entity)}
            : $result;
    }

    // }}}

    // {{{ Check for unique

    /**
     * Get conflict object
     *
     * @param string                      $cleanURL Clean url
     * @param \XLite\Model\AEntity|string $entity   Entity or class name
     * @param mixed                       $id       Entity identifier OPTIONAL
     *
     * @return \XLite\Model\AEntity
     */
    public function getConflict($cleanURL, $entity, $id = null)
    {
        $entityType = static::getEntityType($entity);

        $method = $entityType
            ? __FUNCTION__ . \XLite\Core\Converter::convertToCamelCase($entityType)
            : null;

        if (method_exists($this, $method)) {
            $result = $this->{$method}($cleanURL, $entity, $id);

        } else {
            if (in_array($cleanURL, static::getConfigCleanUrlAliases())) {
                return $this->getCleanUrlTargetEntity($cleanURL);
            }

            if (static::isRootDirExists($cleanURL)) {
                return $this->getCleanUrlRootDirEntity($cleanURL);
            }

            /** @var \XLite\Model\CleanURL $cleanURLObject */
            $cleanURLObject = $this->findConflictByCleanURL($cleanURL, $entity, $id);
            $tmpEntity = $cleanURLObject
                ? $cleanURLObject->getEntity()
                : null;

            $result = ($tmpEntity /* && $cleanURL == $tmpEntity->getCleanURL() */ )
                ? $tmpEntity
                : null;
        }

        return $result;

    }

    /**
     * Check for unique
     *
     * @param string                      $cleanURL Clean url
     * @param \XLite\Model\AEntity|string $entity   Entity or class name
     * @param mixed                       $id       Entity identifier OPTIONAL
     *
     * @return boolean
     */
    public function isURLUnique($cleanURL, $entity, $id = null)
    {
        $result = null;
        $conflict = $this->getConflict($cleanURL, $entity, $id);

        if (is_object($entity)) {
            $entityIdentifier = $entity->getUniqueIdentifier();

            $result = $conflict === null
                || (
                    $entityIdentifier !== null
                    && $conflict->getUniqueIdentifier() === $entityIdentifier
                );

        } elseif ($id) {
            $result = $conflict === null
                || (\Doctrine\Common\Util\ClassUtils::getClass($conflict) === $entity
                    && $conflict->getUniqueIdentifier() == $id
                );

        } else {
            $result = $conflict === null;
        }

        return $result;
    }

    /**
     * Check for unique
     *
     * @param string                      $cleanURL Clean url
     * @param \XLite\Model\AEntity|string $entity   Entity or class name
     * @param mixed                       $id       Entity identifier OPTIONAL
     *
     * @return \XLite\Model\AEntity
     */
    protected function getConflictCategory($cleanURL, $entity, $id = null)
    {
        $result = null;

        if (!is_object($entity)) {
            $entity = \XLite\Core\Database::getRepo('XLite\Model\Category')->find($id);
        }

        /** @var \XLite\Model\Category $entity */
        if ($entity) {
            if (static::isCategoryUrlCanonical()) {
                if (in_array($cleanURL, static::getConfigCleanUrlAliases())) {
                    return $this->getCleanUrlTargetEntity($cleanURL);
                }

                if (static::isRootDirExists($cleanURL)) {
                    return $this->getCleanUrlRootDirEntity($cleanURL);
                }

                $tmpEntity = $this->findEntityByURL('category', $cleanURL);
            } else {
                if ($entity->getDepth() <= 0 && in_array($cleanURL, static::getConfigCleanUrlAliases())) {
                    return $this->getCleanUrlTargetEntity($cleanURL);
                }

                $noExtCleanURL = str_replace('.' . static::CLEAN_URL_DEFAULT_EXTENSION, '', $cleanURL);
                if ($entity->getDepth() <= 0 && static::isRootDirExists($noExtCleanURL)) {
                    return $this->getCleanUrlRootDirEntity($cleanURL);
                }

                $tmpEntity = $this->findCategoryByURL($cleanURL, $entity->getParent());
                if (!$tmpEntity && static::isCategoryUrlHasExt()) {
                    $tmpEntity = $this->findCategoryByURL(str_replace('.' . static::CLEAN_URL_DEFAULT_EXTENSION, '', $cleanURL), $entity->getParent());
                }
            }

            if (!$tmpEntity) {
                $tmpEntity = $this->findCategoryConflictWithOtherTypes($cleanURL);
            }

            $result = $tmpEntity ?: null;

        }

        return $result;
    }

    /**
     * @param string $cleanURL
     * @return \XLite\Model\Base\Catalog
     */
    protected function findCategoryConflictWithOtherTypes($cleanURL)
    {
        return $this->findEntityByURL('product', $cleanURL);
    }

    /**
     * Check for unique
     *
     * @param string                      $cleanURL Clean url
     * @param \XLite\Model\AEntity|string $entity   Entity or class name
     * @param mixed                       $id       Entity identifier OPTIONAL
     *
     * @return boolean
     */
    protected function findConflictByCleanURL($cleanURL, $entity, $id = null)
    {
        $entityType = static::getEntityType($entity);

        if (!is_object($entity)) {
            $entity = \XLite\Core\Database::getRepo($entity)->find($id);
        }

        $queryBuilder = $this->createQueryBuilder();

        $queryBuilder->andWhere('c.cleanURL = :cleanURL')
            ->setParameter('cleanURL', $cleanURL)
            ->orderBy('c.id', 'DESC');

        if (is_object($entity) && $entity->isPersistent()) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->isNull('c.' . $entityType),
                    'c.' . $entityType . ' != :entity'
                )
            )
                ->setParameter('entity', $entity);
        }

        return $queryBuilder->getSingleResult();
    }

    // }}}

    // {{{ Post process url

    /**
     * Post process clean URL
     *
     * @param string              $url    URL
     * @param \XLite\Model\AEntity $entity Entity
     * @param boolean             $ignoreExtension Ignore default extension
     *
     * @return string
     */
    protected function postProcessURL($url, $entity, $ignoreExtension = false)
    {
        $entityType = static::getEntityType($entity);

        $method = $entityType
            ? __FUNCTION__ . \XLite\Core\Converter::convertToCamelCase($entityType)
            : null;

        return method_exists($this, $method)
            ? $this->{$method}($url, $entity, $ignoreExtension)
            : $url;
    }

    /**
     * Post process clean URL
     *
     * @param string               $url    URL
     * @param \XLite\Model\AEntity $entity Entity
     * @param boolean             $ignoreExtension Ignore default extension
     *
     * @return string
     */
    protected function postProcessURLProduct($url, $entity, $ignoreExtension = false)
    {
        return $url . ($this->isProductUrlHasExt() && !$ignoreExtension ? '.' . static::CLEAN_URL_DEFAULT_EXTENSION : '');
    }

    /**
     * Post process clean URL
     *
     * @param string               $url    URL
     * @param \XLite\Model\AEntity $entity Entity
     * @param boolean             $ignoreExtension Ignore default extension
     *
     * @return string
     */
    protected function postProcessURLCategory($url, $entity, $ignoreExtension = false)
    {
        return $url . ($this->isCategoryUrlHasExt() && !$ignoreExtension ? '.' . static::CLEAN_URL_DEFAULT_EXTENSION : '');
    }

    // }}}

    // {{{ Parse url

    /**
     * Parse clean URL
     * Return array((string) $target, (array) $params)
     *
     * @param string $url  Main part of a clean URL
     * @param string $last First part before the "url" OPTIONAL
     * @param string $rest Part before the "url" and "last" OPTIONAL
     * @param string $ext  Extension OPTIONAL
     *
     * @return array
     */
    public function parseURL($url, $last = '', $rest = '', $ext = '')
    {
        $target = null;
        $params = [];

        foreach (static::getEntityTypes() as $model => $entityType) {
            $method = $entityType
                ? __FUNCTION__ . \XLite\Core\Converter::convertToCamelCase($entityType)
                : null;

            $result = method_exists($this, $method)
                ? $this->{$method}($url, $last, $rest, $ext)
                : [];

            if ($result) {
                [$target, $params] = $result;
                break;
            }
        }

        if (null === $target) {
            $result = $this->parseURLConfig($url, $last, $rest, $ext);

            if ($result) {
                [$target, $params] = $result;
            }
        }

        return $this->prepareParseURL($url, $last, $rest, $ext, $target, $params);
    }

    /**
     * @return array
     */
    public static function getConfigCleanUrlAliases()
    {
        return (array) \Includes\Utils\ConfigParser::getOptions(['clean_urls_aliases']);
    }

    /**
     * @param $name
     * @return bool
     */
    public static function isRootDirExists($name)
    {
        return \Includes\Utils\FileManager::isDir(LC_DIR_ROOT . $name);
    }

    /**
     * Parse clean URL
     * Return array((string) $target, (array) $params)
     *
     * @param string $url  Main part of a clean URL
     * @param string $last First part before the "url" OPTIONAL
     * @param string $rest Part before the "url" and "last" OPTIONAL
     * @param string $ext  Extension OPTIONAL
     *
     * @return array
     */
    public function parseURLConfig($url, $last = '', $rest = '', $ext = '')
    {
        $aliases = static::getConfigCleanUrlAliases();

        if (($key = array_search($url, $aliases)) !== false) {
            return [$key, []];
        }

        return null;
    }

    /**
     * Try get clean url by passed url
     *
     * @param string $url
     *
     * @return string
     */
    public function buildCleanUrlByString($url)
    {
        $result = '';

        $query = parse_url($url, PHP_URL_QUERY);

        if ($query) {
            parse_str($query, $params);

            $target = isset($params['target']) ? $params['target'] : '';
            $action = isset($params['action']) ? $params['action'] : '';

            $result = \XLite\Core\Converter::buildCleanURL($target, $action, $params);
        }

        return $result;
    }

    /**
     * Hook for modules
     *
     * @param string $url    Main part of a clean URL
     * @param string $last   First part before the "url"
     * @param string $rest   Part before the "url" and "last"
     * @param string $ext    Extension
     * @param string $target Target
     * @param array  $params Additional params
     *
     * @return array
     */
    protected function prepareParseURL($url, $last, $rest, $ext, $target, $params)
    {
        if ('product' === $target) {
            if (!empty($last)) {
                $path = explode('/', $rest);
                $path[] = $last;

                $entity = static::isCategoryUrlCanonical()
                    ? $this->findEntityByURL('category', $last)
                    : $this->findCategoryByPath($path);

                if ($entity && $entity->hasProduct($params['product_id'])) {
                    $params['category_id'] = $entity->getCategoryId();

                }
            } elseif (!empty(\XLite\Core\Request::getInstance()->category_id)) {
                $params['category_id'] = \XLite\Core\Request::getInstance()->category_id;
            }
        }

        return [$target, $params];
    }

    /**
     * Parse clean URL
     * Return array((string) $target, (array) $params)
     *
     * @param string $url  Main part of a clean URL
     * @param string $last First part before the "url" OPTIONAL
     * @param string $rest Part before the "url" and "last" OPTIONAL
     * @param string $ext  Extension OPTIONAL
     *
     * @return array
     */
    protected function parseURLProduct($url, $last = '', $rest = '', $ext = '')
    {
        $result = null;

        $result = $this->findByURL('product', $url . $ext);

        return $result;
    }

    /**
     * Parse clean URL
     * Return array((string) $target, (array) $params)
     *
     * @param string $url  Main part of a clean URL
     * @param string $last First part before the "url" OPTIONAL
     * @param string $rest Part before the "url" and "last" OPTIONAL
     * @param string $ext  Extension OPTIONAL
     *
     * @return array
     */
    protected function parseURLCategory($url, $last = '', $rest = '', $ext = '')
    {
        $result = null;

        if ($url) {
            if (static::isCategoryUrlCanonical()) {
                $result = $this->findByURL('category', $url . $ext);
            } else {
                $path = explode('/', $rest);
                $path[] = $last;

                if (static::isCategoryUrlHasExt()) {
                    foreach ($path as $k => $v) {
                        $path[$k] = str_replace('.' . static::CLEAN_URL_DEFAULT_EXTENSION, '', $v);
                    }
                }

                $path[] = $url . $ext;

                $entity = $this->findCategoryByPath($path);

                if ($entity) {
                    $target = 'category';
                    $params[$entity->getUniqueIdentifierName()] = $entity->getUniqueIdentifier();

                    $result = [$target, $params];
                }
            }
        }

        return $result;
    }

    /**
     * Find target and params by url ant entity type
     *
     * @param string $entityType Entity type
     * @param string $url        URL
     *
     * @return array
     */
    protected function findByURL($entityType, $url)
    {
        $target = null;
        $params = [];

        $entity = $this->findEntityByURL($entityType, $url);

        if ($entity) {
            $target = $entityType;
            $params[$entity->getUniqueIdentifierName()] = $entity->getUniqueIdentifier();
        }

        return $target
            ? [$target, $params]
            : null;
    }

    /**
     * Find Entity by clean URL
     *
     * @param string $entityType Entity type
     * @param string $url        URL
     *
     * @return \XLite\Model\Base\Catalog
     */
    protected function findEntityByURL($entityType, $url)
    {
        $entity = $this->defineFindEntityByURL($entityType, $url)->getSingleResult();

        $method = $entityType
            ? 'get' . \XLite\Core\Converter::convertToCamelCase($entityType)
            : null;

        return $entity && method_exists($entity, $method)
            ? $entity->{$method}()
            : null;
    }

    /**
     * Find Entity by clean URL
     *
     * @param string $entityType Entity type
     * @param string $url        URL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindEntityByURL($entityType, $url)
    {
        $queryBuilder = $this->createQueryBuilder();

        $queryBuilder->andWhere('c.cleanURL = :url')
            ->setParameter('url', $url)
            ->andWhere($queryBuilder->expr()->isNotNull('c.' . $entityType))
            ->orderBy('c.id', 'DESC');

        return $queryBuilder;
    }

    /**
     * Find category by path
     *
     * @param array $path Path
     *
     * @return \XLite\Model\Category
     */
    protected function findCategoryByPath($path)
    {
        $parent = \XLite\Core\Database::getRepo('\XLite\Model\Category')->getRootCategory();

        foreach (array_filter($path) as $categoryURL) {
            $entity = $this->findCategoryByURL($categoryURL, $parent);

            if (empty($entity)) {
                if (static::isCategoryUrlHasExt() && !static::isCategoryUrlCanonical()) {
                    $entity = $this->findCategoryByURL($categoryURL . '.' . static::CLEAN_URL_DEFAULT_EXTENSION, $parent);
                }

                if (empty($entity)) {
                    break;
                }
            }

            $parent = $entity;
        }

        return isset($entity) ? $entity : null;
    }

    /**
     * Find category by clean URL
     *
     * @param string                $url    URL
     * @param \XLite\Model\Category $parent Parent category
     *
     * @return \XLite\Model\Category
     */
    protected function findCategoryByURL($url, $parent)
    {
        return $this->executeCachedRuntime(function () use ($url, $parent) {
            $entity = $this->defineFindCategoryByURL($url, $parent)->getSingleResult();

            return $entity
                ? $entity->getCategory()
                : null;
        }, [
            'findCategoryByURL',
            $url,
            $parent ? $parent->getCategoryId() : ''
        ]);
    }

    /**
     * Find category by clean URL
     *
     * @param string                $url    URL
     * @param \XLite\Model\Category $parent Parent category
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindCategoryByURL($url, $parent)
    {
        $queryBuilder = $this->defineFindEntityByURL('category', $url);

        $queryBuilder->linkInner('c.category')
            ->andWhere('category.parent = :parent')
            ->setParameter('parent', $parent
                ? $parent->getCategoryId()
                : null)
            ->orderBy('c.id', 'DESC');

        return $queryBuilder;
    }

    // }}}

    // {{{ Build URL

    /**
     * Build clean URL
     *
     * @param string  $target         Target
     * @param array   $params         Params OPTIONAL
     *
     * @return string
     */
    public function buildURL($target = '', array $params = [])
    {
        $result = null;

        if (\XLite\Core\Operator::isClassExists($target)) {
            $target = static::getEntityType($target);
        }

        $method = $target
            ? __FUNCTION__ . \XLite\Core\Converter::convertToCamelCase($target)
            : null;

        $data = method_exists($this, $method)
            ? $this->{$method}($params)
            : [];

        if ($data) {
            [$urlParts, $params] = $data;
            $result = $this->buildUrlByData($target, $urlParts, $params);
        } else {
            $aliases = static::getConfigCleanUrlAliases();

            if (isset($aliases[$target])) {
                return (substr($aliases[$target], -4) == '.' . static::CLEAN_URL_DEFAULT_EXTENSION)
                    ? $aliases[$target]
                    : $aliases[$target] . '/';
            }
        }

        return $result;
    }

    /**
     * Build url string by array
     *
     * @param string $target
     * @param array  $urlParts
     * @param array  $params
     *
     * @return string
     */
    public function buildUrlByData($target, $urlParts, $params)
    {
        $result = null;

        if ($urlParts) {
            [$urlParts, $params] = $this->prepareBuildURL($target, $params, $urlParts);
        }

        if ($urlParts) {
            unset($params['target']);
            $result = implode('/', array_reverse($urlParts));

            if ($target == 'category' && !static::isCategoryUrlHasExt()) {
                $result .= '/';
            }

            if (!empty($params)) {
                $result .= '?' . http_build_query($params, null, '&');
            }
        }

        return $result;
    }

    /**
     * Returns 'category_clean_urls_format' option value
     *
     * @return string
     */
    public static function getCategoryCleanUrlFormat()
    {
        $format = \Includes\Utils\ConfigParser::getOptions(['clean_urls', 'category_clean_urls_format']);

        return in_array($format, [
            static::CATEGORY_URL_FORMAT_NON_CANONICAL_NO_EXT,
            static::CATEGORY_URL_FORMAT_CANONICAL_NO_EXT,
            static::CATEGORY_URL_FORMAT_NON_CANONICAL_EXT,
            static::CATEGORY_URL_FORMAT_CANONICAL_EXT,
        ])
            ? $format
            : static::CATEGORY_URL_FORMAT_NON_CANONICAL_NO_EXT;
    }

    /**
     * Returns 'use_canonical_urls_only' option value
     *
     * @return boolean
     */
    public function isUseCanonicalProductURLWithCategory()
    {
        return static::PRODUCT_URL_CANONICAL_WITH_CATEGORY === \Includes\Utils\ConfigParser::getOptions(['clean_urls', 'canonical_product_clean_urls_format']);
    }

    /**
     * Is use canonical url for categories
     *
     * @return boolean
     */
    public static function isCategoryUrlCanonical()
    {
        return in_array(static::getCategoryCleanUrlFormat(), [
            static::CATEGORY_URL_FORMAT_CANONICAL_NO_EXT,
            static::CATEGORY_URL_FORMAT_CANONICAL_EXT,
        ]);
    }

    /**
     * Is use extension for categories
     *
     * @return boolean
     */
    public static function isCategoryUrlHasExt()
    {
        return in_array(static::getCategoryCleanUrlFormat(), [
            static::CATEGORY_URL_FORMAT_NON_CANONICAL_EXT,
            static::CATEGORY_URL_FORMAT_CANONICAL_EXT,
        ]);
    }

    /**
     * Returns 'product_clean_urls_format' option value
     *
     * @return string
     */
    public static function getProductCleanUrlFormat()
    {
        $format = \Includes\Utils\ConfigParser::getOptions(['clean_urls', 'product_clean_urls_format']);

        return in_array($format, [
            static::PRODUCT_URL_FORMAT_EXT,
            static::PRODUCT_URL_FORMAT_NO_EXT
        ])
            ? $format
            : static::PRODUCT_URL_FORMAT_NO_EXT;
    }

    /**
     * Is use extension for categories
     *
     * @return boolean
     */
    public static function isProductUrlHasExt()
    {
        return static::getProductCleanUrlFormat() === static::PRODUCT_URL_FORMAT_EXT;
    }

    /**
     * Hook for modules
     *
     * @param string $target   Target
     * @param array  $params   Params
     * @param array  $urlParts URL parts
     *
     * @return array
     */
    protected function prepareBuildURL($target, $params, $urlParts)
    {
        if ('product' === $target) {
            /** @var \XLite\Model\Repo\Category $repo */
            $repo = \XLite\Core\Database::getRepo('XLite\Model\Category');

            if (!empty($params['category_id'])) {
                if ($repo->hasProduct($params['category_id'], $params['product_id'])) {
                    $categoryUrlParts = $this->getCategoryURLPath($params['category_id']);

                    if ($this->isCategoryUrlHasExt()) {
                        foreach ($categoryUrlParts as $k => $v) {
                            $categoryUrlParts[$k] = str_replace('.' . static::CLEAN_URL_DEFAULT_EXTENSION, '', $v);
                        }
                    }

                    if ($categoryUrlParts) {
                        $urlParts = array_merge($urlParts, $categoryUrlParts);

                        unset($params['category_id']);
                    }

                } else {
                    unset($params['category_id']);
                }
            }

            unset($params['product_id']);
        }

        return [$urlParts, $params];
    }

    /**
     * Build product URL
     *
     * @param array  $params Params
     *
     * @return array
     */
    protected function buildURLProduct($params)
    {
        $urlParts = [];

        if (!empty($params['product_id'])) {
            /** @var \XLite\Model\Product $product */
            $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($params['product_id']);

            if (null !== $product && $product->getCleanURL()) {
                $urlParts[] = $product->getCleanURL();
            }
        }

        return [$urlParts, $params];
    }

    /**
     * Build canonical product URL
     *
     * @param array  $params Params
     *
     * @return array
     */
    public function buildURLProductCanonical($params)
    {
        [$urlParts, $params] = $this->buildURLProduct($params);

        if ($this->isUseCanonicalProductURLWithCategory()) {
            if ($product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($params['product_id'])) {
                /** @var \XLite\Model\Product $product */
                if ($categoryId = $product->getCategoryId()) {
                    $categoryUrlParts = $this->getCategoryURLPath($categoryId);

                    if ($categoryUrlParts && $this->isCategoryUrlHasExt()) {
                        foreach ($categoryUrlParts as $k => $v) {
                            $categoryUrlParts[$k] = str_replace('.' . static::CLEAN_URL_DEFAULT_EXTENSION, '', $v);
                        }
                    }

                    if ($categoryUrlParts) {
                        $urlParts = array_merge($urlParts, $categoryUrlParts);
                    }
                }
            }
        }

        $result = implode('/', array_reverse($urlParts));

        return $result;
    }

    /**
     * Build category URL
     *
     * @param array  $params Params
     *
     * @return array
     */
    protected function buildURLCategory($params)
    {
        $urlParts = [];

        $id = isset($params['category_id'])
            ? $params['category_id']
            : (isset($params['id']) ? $params['id'] : null);
        if ($id) {
            $urlParts = $this->getCategoryURLPath($id);

            if ($urlParts) {
                unset($params['category_id']);
                unset($params['id']);
            }
        }

        return [$urlParts, $params];
    }

    protected function buildURLMain($params)
    {
        return [[''], $params];
    }

    /**
     * Returns category url path
     *
     * @param integer $categoryId Category id
     *
     * @return array
     */
    protected function getCategoryURLPath($categoryId)
    {
        $category = \XLite\Core\Database::getRepo('XLite\Model\Category')->getCategory($categoryId);

        if (!$category) {
            return null;
        }

        if (static::isCategoryUrlCanonical()) {
            return [$category->getCleanURL()];
        } else {
            $result = [];
            $hasExt = static::isCategoryUrlHasExt();

            $categories = $category->getPath();
            $lastIndex = count($categories) - 1;
            for ($i = $lastIndex; $i >= 0; $i--) {
                $cleanUrl = $categories[$i]->getCleanURL();
                if ($cleanUrl) {
                    $result[] = $hasExt && $i !== $lastIndex
                        ? str_replace('.' . static::CLEAN_URL_DEFAULT_EXTENSION, '', $cleanUrl)
                        : $cleanUrl;
                } else {
                    return null;
                }
            }

            return $result;
        }
    }

    // }}}

    // {{{ Fake url with placeholder

    /**
     * Build fake url with placeholder
     *
     * @param \XLite\Model\AEntity|string $entity Entity
     * @param array                       $params Params OPTIONAL
     * @param boolean                     $ignoreExtension Ignore default extension OPTIONAL
     *
     * @return string
     */
    public function buildFakeURL($entity, array $params = [], $ignoreExtension = false)
    {
        $result = '';

        $entityType = static::getEntityType($entity);

        $method = $entityType
            ? __FUNCTION__ . \XLite\Core\Converter::convertToCamelCase($entityType)
            : null;

        $data = method_exists($this, $method)
            ? $this->{$method}($entity, $params, $ignoreExtension)
            : [];

        if ($data) {
            [$urlParts, $params] = $data;
            [$urlParts, $params] = $this->prepareBuildURL($entityType, $params, $urlParts);

            if ($urlParts) {
                unset($params['target']);
                $result = implode('/', array_reverse($urlParts));

                if ($entityType == 'category' && !static::isCategoryUrlHasExt()) {
                    $result .= '/';
                }

                if (!empty($params)) {
                    $result .= '?' . http_build_query($params, null, '&');
                }
            }
        }

        return $result;
    }

    /**
     * Build fake url with placeholder
     *
     * @param \XLite\Model\AEntity|string $entity Entity
     * @param array                       $params Params
     * @param boolean                     $ignoreExtension Ignore default extension
     *
     * @return string
     */
    protected function buildFakeURLProduct($entity, $params, $ignoreExtension = false)
    {
        $urlParts = [$this->postProcessURL(static::PLACEHOLDER, $entity, $ignoreExtension)];

        /** @var \XLite\Model\Product $entity */
//        if (is_object($entity) && $entity->getCategoryId()) {
//            $params['category_id'] = $entity->getCategoryId();
//        }

        return [$urlParts, $params];
    }

    /**
     * Build fake url with placeholder
     *
     * @param \XLite\Model\AEntity|string $entity Entity
     * @param array                       $params Params
     * @param boolean                     $ignoreExtension Ignore default extension
     *
     * @return string
     */
    protected function buildFakeURLCategory($entity, $params, $ignoreExtension = false)
    {
        $urlParts = [$this->postProcessURL(static::PLACEHOLDER, $entity, $ignoreExtension)];

        /** @var \XLite\Model\Category $entity */
        if (is_object($entity) && $entity->getParentId() && !static::isCategoryUrlCanonical()) {
            $urlParts = array_merge($urlParts, $this->getCategoryURLPath($entity->getParentId()));
        }

        return [$urlParts, $params];
    }

    // }}}

    // {{{ Edit url

    /**
     * Build edit url
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return string
     */
    public function buildEditURL($entity)
    {
        $result = '';

        $entityType = static::getEntityType($entity);

        $method = $entityType
            ? __FUNCTION__ . \XLite\Core\Converter::convertToCamelCase($entityType)
            : null;

        $data = method_exists($this, $method)
            ? $this->{$method}($entity)
            : [
                \XLite\Core\Converter::convertFromCamelCase($entityType),
                [$entity->getUniqueIdentifierName() => $entity->getUniqueIdentifier()]
            ];

        if ($data) {
            [$target, $params] = $data;

            if ($target) {
                $result = \XLite\Core\Converter::buildURL($target, '', $params);
            }
        }

        return $result;
    }

    /**
     * Build edit url
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return array
     */
    protected function buildEditURLCategory($entity)
    {
        return ['category', ['id' => $entity->getUniqueIdentifier()]];
    }

    /**
     * @param $cleanURL
     *
     * @return \XLite\Model\TargetCleanUrl
     */
    public function getCleanUrlTargetEntity($cleanURL)
    {
        $entity = new \XLite\Model\TargetCleanUrl();
        $entity->setCleanURL($cleanURL);

        return $entity;
    }

    /**
     * @param $cleanURL
     *
     * @return \XLite\Model\RootDirCleanUrl
     */
    public function getCleanUrlRootDirEntity($cleanURL)
    {
        $entity = new \XLite\Model\RootDirCleanUrl();
        $entity->setCleanURL($cleanURL);

        return $entity;
    }

    // }}}
}
