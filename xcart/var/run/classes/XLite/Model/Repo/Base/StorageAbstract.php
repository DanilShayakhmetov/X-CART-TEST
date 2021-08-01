<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\Base;

/**
 * Storage abstract repository
 */
abstract class StorageAbstract extends \XLite\Model\Repo\ARepo
{
    /**
     * Get storage name
     *
     * @return string
     */
    abstract public function getStorageName();

    /**
     * Get file system images storage root path
     *
     * @return string
     */
    abstract public function getFileSystemRoot();

    /**
     * Get web images storage root path
     *
     * @return string
     */
    abstract public function getWebRoot();

    /**
     * Check - store remote image into local file system or not
     *
     * @return boolean
     */
    public function isStoreRemote()
    {
        return false;
    }

    /**
     * Get allowed file system root list
     *
     * @return array
     */
    public function getAllowedFileSystemRoots()
    {
        $result   = [];
        $result[] = $this->getFileSystemRoot();

        return $result;
    }

    // {{{ Remove cross-repository files

    /**
     * Has one or more entity with specified path
     *
     * @param string                    $path   Path
     * @param \XLite\Model\Base\Storage $entity Exclude entity
     *
     * @return boolean
     */
    public function findOneByFullPath($path, \XLite\Model\Base\Storage $entity)
    {
        $id = ($this->getEntityName() === get_class($entity) || is_subclass_of($entity, $this->getEntityName()))
            ? $entity->getId()
            : null;

        $found = 0 < (int) $this->defineFindOneByFullPathQuery($path, true, $id)->getSingleScalarResult();
        if (!$found) {
            $root = $this->getFileSystemRoot();
            if (0 === strncmp($root, $path, strlen($root))) {
                $path  = substr($path, strlen($root));
                $found = 0 < (int) $this->defineFindOneByFullPathQuery($path, false, $id)->getSingleScalarResult();
            }
        }

        return $found;
    }

    /**
     * Find storages by full path
     *
     * @param string                    $path   Path
     * @param \XLite\Model\Base\Storage $entity Exclude path
     *
     * @return array
     */
    public function findByFullPath($path, \XLite\Model\Base\Storage $entity)
    {
        $id = ($this->getEntityName() === get_class($entity) || is_subclass_of($entity, $this->getEntityName()))
            ? $entity->getId()
            : null;

        $absolute = $this->defineFindByFullPathQuery($path, true, $id)->getResult();
        $root     = $this->getFileSystemRoot();
        if (0 === strncmp($root, $path, strlen($root))) {
            $path     = substr($path, strlen($root));
            $relative = $this->defineFindByFullPathQuery($path, false, $id)->getResult();

        } else {
            $relative = [];
        }

        return array_merge($absolute, $relative);
    }

    /**
     * Check - allow remove path or not
     *
     * @param string                    $path   Path
     * @param \XLite\Model\Base\Storage $entity Exclude entity
     *
     * @return boolean
     * @throws \Exception
     */
    public function allowRemovePath($path, \XLite\Model\Base\Storage $entity)
    {
        foreach ($this->defineStorageRepositories() as $class) {
            if (\XLite\Core\Database::getRepo($class)->findOneByFullPath($path, $entity)) {
                return false;
            }
        }

        $uow = $this->getEntityManager()->getUnitOfWork();

        $classes = array_map(function ($e) {
            return ltrim($e, '\\');
        }, $this->defineStorageRepositories());

        foreach ($uow->getScheduledEntityInsertions() as $e) {
            /* @var \XLite\Model\AEntity|\XLite\Model\Base\Storage $e */
            if (
                in_array($e->getEntityName(), $classes)
                && $entity !== $e
                && $entity->getStoragePath() === $e->getStoragePath()
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Define all storage-based repositories classes list
     *
     * @return array
     */
    protected function defineStorageRepositories()
    {
        return [
            'XLite\Model\Image\Product\Image',
            'XLite\Model\Image\Category\Image',
            'XLite\Model\Image\Category\Banner',
        ];
    }

    /**
     * Define query for findOneByFull() method
     *
     * @param string  $path     Path
     * @param boolean $absolute Absolute path flag
     * @param integer $id       Excluding entity id OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder|\XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneByFullPathQuery($path, $absolute, $id = null)
    {
        $deletions                = [];
        $scheduledEntityDeletions = \XLite\Core\Database::getEM()->getUnitOfWork()->getScheduledEntityDeletions();
        /** @var \XLite\Model\Base\Storage $className */
        $className = $this->getClassName();
        foreach ($scheduledEntityDeletions as $scheduledEntityDeletion) {
            if ($scheduledEntityDeletion instanceof $className) {
                $deletions[] = $scheduledEntityDeletion->getUniqueIdentifier();
            }
        }

        $qb = $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->andWhere('s.path = :path AND s.storageType = :stype')
            ->setParameter('path', $path)
            ->setParameter(
                'stype',
                $absolute ? \XLite\Model\Base\Storage::STORAGE_ABSOLUTE : \XLite\Model\Base\Storage::STORAGE_RELATIVE
            );

        if ($id) {
            $deletions[] = $id;
        }

        if ($deletions) {
            $qb->andWhere($qb->expr()->notIn('s.id', $deletions));
        }

        return $qb;
    }

    /**
     * Define query for findByFullPath() method
     *
     * @param string  $path     Path
     * @param boolean $absolute Absolute path flag
     * @param integer $id       Excluding entity id OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder|\XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindByFullPathQuery($path, $absolute, $id = null)
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.path = :path AND s.storageType = :stype')
            ->setParameter('path', $path)
            ->setParameter(
                'stype',
                $absolute ? \XLite\Model\Base\Storage::STORAGE_ABSOLUTE : \XLite\Model\Base\Storage::STORAGE_RELATIVE
            );

        if ($id) {
            $qb->andWhere('s.id != :id')->setParameter('id', $id);
        }

        return $qb;
    }

    // }}}

    // {{{ Fixtures

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
    public function loadRawFixture(\XLite\Model\AEntity $entity, array $record, array $regular = [], array $assocs = [])
    {
        parent::loadRawFixture($entity, $record, $regular, $assocs);

        if (!empty($record['loadURL'])) {
            $entity->loadFromURL($record['loadURL']);
        }
    }

    // }}}
}
