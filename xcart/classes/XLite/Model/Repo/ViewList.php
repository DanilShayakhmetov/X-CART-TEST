<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;
use XLite\Core\Layout;

/**
 * View list repository
 *
 * @Api\Operation\Read(modelClass="XLite\Model\ViewList", summary="Retrieve view list item by id")
 * @Api\Operation\ReadAll(modelClass="XLite\Model\ViewList", summary="Retrieve all view list items")
 */
class ViewList extends \XLite\Model\Repo\ARepo
{
    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_INTERNAL;

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = [
        'weight' => true,
        'child'  => true,
        'tpl'    => true,
    ];

    /**
     * Define cache cells
     *
     * @return array
     */
    protected function defineCacheCells()
    {
        $list = parent::defineCacheCells();
        $list['class_list'] = [
            static::ATTRS_CACHE_CELL => ['list', 'zone'],
        ];

        return $list;
    }

    // {{{ Finders

    /**
     * Find class list
     *
     * @param string $list List name
     * @param string $zone Current interface name OPTIONAL
     *
     * @return array
     */
    public function findClassList($list, $zone = \XLite\Model\ViewList::INTERFACE_CUSTOMER)
    {
        $data = $this->getFromCache('class_list', ['list' => $list, 'zone' => $zone, 'preset' => $this->getCurrentPreset()]);
        if (!isset($data)) {
            $data = $this->retrieveClassList($list, $zone);
            $this->saveToCache($data, 'class_list', ['list' => $list, 'zone' => $zone, 'preset' => $this->getCurrentPreset()]);
        }

        return $data;
    }

    /**
     * Find class list
     *
     * @param string $list List name
     * @param string $zone Current interface name OPTIONAL
     *
     * @return array
     */
    public function findClassListWithFallback($list, $zone = \XLite\Model\ViewList::INTERFACE_CUSTOMER)
    {
        $data = $this->getFromCache(
            'class_list_with_fallback',
            ['list' => $list, 'zone' => $zone]
        );

        if (!isset($data)) {
            $data = $this->retrieveClassListWithFallback($list, $zone);
            $this->saveToCache(
                $data,
                'class_list_with_fallback',
                ['list' => $list, 'zone' => $zone]
            );
        }

        return $data;
    }

    /**
     * Find actual (with empty version) by list name
     *
     * @param string $list List name
     *
     * @return array
     */
    public function findActualByList($list)
    {
        return $this->createQueryBuilder()
            ->where('v.list = :list AND v.version IS NOT NULL')
            ->setParameter('list', $list)
            ->getResult();
    }

    /**
     * Perform Class list query
     *
     * @param string $list List name
     * @param string $zone Current interface name
     *
     * @return array
     */
    public function retrieveClassList($list, $zone)
    {
        return $this->defineClassListQuery($list, $zone)->getResult();
    }

    /**
     * Perform Class list query
     *
     * @param string $list List name
     * @param string $zone Current interface name
     *
     * @return array
     */
    public function retrieveClassListWithFallback($list, $zone)
    {
        $result = [];

        $actual = $this->defineClassListWithFallbackQuery($list, $zone)->getResult();

        foreach ($actual as $viewList) {
            $key = $viewList->getHashWithoutZone();

            if (!isset($result[$key])
                || $result[$key]->getZone() === \XLite::COMMON_INTERFACE
            ) {
                $result[$key] = $viewList;
            }
        }

        return $result;
    }

    /**
     * Define default query builder for findClassList() without zone parameter
     *
     * @param string $list Class list name
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineZoneAgnosticClassListQuery($list)
    {
        $qb = $this->createQueryBuilder()
            ->addSelect('CASE WHEN v.override_mode > 0 THEN v.weight_override ELSE v.weight END AS HIDDEN ORD')
            ->andWhere('IF (v.list_override != :empty AND v.override_mode > 0, v.list_override, v.list) IN (:list)')
            ->andWhere('v.list_id NOT IN (SELECT DISTINCT IDENTITY(vl.parent) FROM XLite\Model\ViewList vl WHERE IDENTITY(vl.parent) IS NOT NULL AND vl.preset LIKE :preset AND vl.override_mode != :disable_preset_mode)')
            ->andWhere('v.list_id NOT IN (SELECT DISTINCT vll.list_id FROM XLite\Model\ViewList vll WHERE IDENTITY(vll.parent) IS NOT NULL AND (vll.preset NOT LIKE :preset OR vll.override_mode = :disable_preset_mode))')
            ->andWhere('v.version IS NULL')
            ->andWhere('v.override_mode IN (:modes)')
            ->andWhere('v.deleted = :deleted')
            ->orderBy('ORD', 'asc')
            ->setParameter('empty', '')
            ->setParameter('list', explode(',', $list))
            ->setParameter('preset', $this->getCurrentPreset())
            ->setParameter('modes', $this->getDisplayableModes())
            ->setParameter('deleted', false)
            ->setParameter('disable_preset_mode', \XLite\Model\ViewList::OVERRIDE_DISABLE_PRESET);

        return $qb;
    }

    /**
     * Define query builder for findClassList()
     *
     * @param string $list Class list name
     * @param string $zone Current interface name
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineClassListQuery($list, $zone)
    {
        $qb = $this->defineZoneAgnosticClassListQuery($list)
            ->andWhere('v.zone LIKE :zone')
            ->setParameter('zone', $zone);

        return $qb;
    }

    /**
     * Define query builder for findClassList()
     *
     * @param string $list Class list name
     * @param string $zone Current interface name
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineClassListWithFallbackQuery($list, $zone)
    {
        $qb = $this->defineZoneAgnosticClassListQuery($list)
            ->andWhere('v.zone IN (:zone, :fallback)')
            ->setParameter('zone', $zone)
            ->setParameter('fallback', \XLite::COMMON_INTERFACE);

        return $qb;
    }

    /**
     * @return array
     */
    protected function getDisplayableModes()
    {
        return [
            \XLite\Model\ViewList::OVERRIDE_OFF,
            \XLite\Model\ViewList::OVERRIDE_MOVE
        ];
    }

    /**
     * @return string
     */
    protected function getCurrentPreset()
    {
        return Layout::getInstance()->getCurrentLayoutPreset();
    }

    // }}}

    // {{{ Operations

    /**
     * Delete obsolete view list childs
     *
     * @param string $currentVersion Current version
     *
     * @return void
     */
    public function deleteObsolete($currentVersion)
    {
        $this->defineDeleteObsoleteQuery($currentVersion)
            ->execute();
    }

    /**
     * Mark current view list childs as default
     *
     * @param string $currentVersion Current version
     *
     * @return void
     */
    public function markCurrentVersion($currentVersion)
    {
        $this->defineMarkCurrentVersionQuery($currentVersion)
            ->execute();
    }

    /**
     * Define query for deleteObsolete() method
     *
     * @param string $currentVersion Current version
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineDeleteObsoleteQuery($currentVersion)
    {
        return $this->createPureQueryBuilder('v', false)
            ->delete($this->_entityName, 'v')
            ->andWhere('v.version != :version OR v.version IS NULL')
            ->setParameter('version', $currentVersion);
    }

    /**
     * Define query for markCurrentVersion() method
     *
     * @param string $currentVersion Current version
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineMarkCurrentVersionQuery($currentVersion)
    {
        return $this->createPureQueryBuilder('v', false)
            ->update($this->_entityName, 'v')
            ->set('v.version', 'NULL')
            ->andWhere('v.version = :version')
            ->setParameter('version', $currentVersion);
    }

    // }}}

    /**
     * Find overridden view list items
     *
     * @return array
     */
    public function findOverridden()
    {
        return $this->defineOverriddenQueryBuilder()->getResult();
    }

    /**
     * Define overridden query builder
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function defineOverriddenQueryBuilder()
    {
        return $this->createQueryBuilder()
            ->where('v.override_mode > :off_mode')
            ->andWhere('v.version IS NULL')
            ->setParameter('off_mode', \XLite\Model\ViewList::OVERRIDE_OFF);
    }

    /**
     * Find first entity equal to $other
     *
     * @param \XLite\Model\ViewList $other     Other entity
     * @param boolean               $versioned Add `version is not null` condition
     *
     * @return \XLite\Model\ViewList|null
     */
    public function findEqual(\XLite\Model\ViewList $other, $versioned = false)
    {
        if (!$other) {
            return null;
        }

        $conditions = [
            'list'   => $other->getList(),
            'child'  => $other->getChild(),
            'tpl'    => $other->getTpl(),
            'zone'   => $other->getZone(),
            'weight' => $other->getWeight(),
            'preset' => $other->getPreset()
        ];

        return $this->findEqualByData($conditions, $versioned);
    }

    /**
     * Find first entity equal to $other
     *
     * @param \XLite\Model\ViewList $other     Other entity
     * @param boolean               $versioned Add `version is not null` condition
     *
     * @return \XLite\Model\ViewList|null
     */
    public function findEqualParent(\XLite\Model\ViewList $other, $versioned = false)
    {
        if (!$other) {
            return null;
        }

        $conditions = [
            'list'          => $other->getList(),
            'child'         => $other->getChild(),
            'tpl'           => $other->getTpl(),
            'zone'          => $other->getZone(),
            'parent'        => null,
            'override_mode' => \XLite\Model\ViewList::OVERRIDE_OFF,
        ];

        return $this->findEqualByData($conditions, $versioned);
    }

    /**
     * Find first entity equal to data
     *
     * @param array   $conditions
     * @param boolean $versioned Add `version is not null` condition
     *
     * @return \XLite\Model\ViewList|null
     */
    public function findEqualByData($conditions, $versioned = false)
    {
        $params = array_filter($conditions, function ($item) {
            return $item !== null;
        });

        $qb = $this->createQueryBuilder()->setParameters($params);

        foreach ($conditions as $key => $condition) {
            if ($condition === null) {
                $qb->andWhere("v.{$key} IS NULL");
            } else {
                $qb->andWhere("v.{$key} = :{$key}");
            }
        }

        if ($versioned) {
            $qb->andWhere('v.version IS NOT NULL');
        }

        return $qb->getSingleResult();
    }
}

