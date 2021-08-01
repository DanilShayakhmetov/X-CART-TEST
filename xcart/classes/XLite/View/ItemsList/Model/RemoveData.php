<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * Remove data items list
 */
class RemoveData extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Types
     */
    const TYPE_PRODUCTS               = 'products';
    const TYPE_CATEGORIES             = 'categories';
    const TYPE_ORDERS                 = 'orders';
    const TYPE_CUSTOMERS              = 'customers';
    const TYPE_CLASSES_AND_ATTRIBUTES = 'classesAndAttributes';

    /**
     * Cached list
     *
     * @var   array
     */
    protected $cachedList;

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    protected function getFormOptions()
    {
        return array_merge(
            parent::getFormOptions(),
            [
                'action' => $this->getFormAction(),
            ]
        );
    }

    protected function getFormTarget()
    {
        return 'remove_data';
    }

    protected function getFormAction()
    {
        return 'remove_data';
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['remove_data']);
    }

    /**
     * Check if header is visible
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return true;
    }


    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'name' => [
                static::COLUMN_MAIN    => true,
                static::COLUMN_ORDERBY => 100,
            ],
        ];
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return null;
    }

    /**
     * Return entities list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        if (null === $this->cachedList) {
            $this->cachedList = [];
            foreach ($this->getPlainData() as $id => $cell) {
                $this->cachedList[] = new \XLite\Model\RemoveDataCell(['id' => $id] + $cell);
            }
        }

        return $countOnly ? count($this->cachedList) : $this->cachedList;
    }

    /**
     * Get plain data
     *
     * @return array
     */
    protected function getPlainData()
    {
        return [
            static::TYPE_PRODUCTS               => [
                'name' => static::t('Products'),
            ],
            static::TYPE_CATEGORIES             => [
                'name' => static::t('Categories'),
            ],
            static::TYPE_ORDERS                 => [
                'name' => static::t('Orders'),
            ],
            static::TYPE_CUSTOMERS              => [
                'name' => static::t('Customers'),
            ],
            static::TYPE_CLASSES_AND_ATTRIBUTES => [
                'name' => static::t('Classes & Global Attributes'),
            ],
        ];
    }

    /**
     * Check - remove entity or not
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        $method = $this->buildMethodName($entity, 'isAllowRemove%s');

        return $method && false !== $this->$method();
    }

    /**
     * Check - allow remove products or not
     *
     * @return boolean
     */
    protected function isAllowRemoveProducts()
    {
        return 0 < \XLite\Core\Database::getRepo('XLite\Model\Product')->count();
    }

    /**
     * Check - allow remove categories or not
     *
     * @return boolean
     */
    protected function isAllowRemoveCategories()
    {
        return 1 < \XLite\Core\Database::getRepo('XLite\Model\Category')->count();
    }

    /**
     * Check - allow remove orders or not
     *
     * @return boolean
     */
    protected function isAllowRemoveOrders()
    {
        return 0 < \XLite\Core\Database::getRepo('XLite\Model\Order')->count();
    }

    /**
     * Check - allow remove orders or not
     *
     * @return boolean
     */
    protected function isAllowRemoveClassesAndAttributes()
    {
        return 0 < \XLite\Core\Database::getRepo('XLite\Model\ProductClass')->count()
            || 0 < \XLite\Core\Database::getRepo('XLite\Model\Attribute')->countForRemoveGlobalAttributesData()
            || 0 < \XLite\Core\Database::getRepo('XLite\Model\AttributeGroup')->count();
    }

    /**
     * Check - allow remove customers or not
     *
     * @return boolean
     */
    protected function isAllowRemoveCustomers()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE} = 'C';

        $countC = \XLite\Core\Database::getRepo('XLite\Model\Profile')->search($cnd, true);

        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Profile::SEARCH_USER_TYPE} = 'N';

        $countN = \XLite\Core\Database::getRepo('XLite\Model\Profile')->search($cnd, true);

        return 0 < ($countC + $countN);
    }

    /**
     * Build method name
     *
     * @param \XLite\Model\AEntity $entity  Entity
     * @param string               $pattern Pattern
     *
     * @return string
     */
    protected function buildMethodName(\XLite\Model\AEntity $entity, $pattern)
    {
        $name = '';
        switch ($entity->getId()) {
            case static::TYPE_PRODUCTS:
            case static::TYPE_CATEGORIES:
            case static::TYPE_ORDERS:
            case static::TYPE_CUSTOMERS:
            case static::TYPE_CLASSES_AND_ATTRIBUTES:
                $name = ucfirst($entity->getId());
                break;
        }

        return $name ? sprintf($pattern, $name) : null;
    }

    // {{{ Process

    protected function findForRemove($id)
    {
        return null;
    }

    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        return false;
    }

    // }}}

    // {{{ Behaviors

    protected function isRemoved()
    {
        return true;
    }

    // }}}

    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' remove-data';
    }

    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\ItemsList\RemoveData';
    }
}
