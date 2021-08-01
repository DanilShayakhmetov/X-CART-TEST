<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * Zones items list
 */
class Zone extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'items_list/model/table/zones/style.less';
        $list[] = 'zones/style.less';

        return $list;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'zone_name' => [
                static::COLUMN_NAME      => \XLite\Core\Translation::lbl('Zone'),
                static::COLUMN_CLASS     => '\XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS    => ['required' => true],
                static::COLUMN_ORDERBY   => 100,
                static::COLUMN_MAIN      => true,
                static::COLUMN_LINK      => 'zones',
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
        return '\XLite\Model\Zone';
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'Create zone';
    }

    /**
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * Inline creation mechanism position
     *
     * @return integer
     */
    protected function isInlineCreation()
    {
        return static::CREATE_INLINE_TOP;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Get remove message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getRemoveMessage($count)
    {
        return \XLite\Core\Translation::lbl('X zones have been removed', ['count' => $count]);
    }

    /**
     * Get create message
     *
     * @param integer $count Count
     *
     * @return string
     */
    protected function getCreateMessage($count)
    {
        return \XLite\Core\Translation::lbl('X zones have been successfully created', ['count' => $count]);
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' zones';
    }

    /**
     * Get pager parameters
     *
     * @return array
     */
    protected function getPagerParams()
    {
        $params = parent::getPagerParams();

        $params[\XLite\View\Pager\APager::PARAM_ITEMS_PER_PAGE] = 50;

        return $params;
    }

    /**
     * Disable removing default zone
     *
     * @param \XLite\Model\Language $entity Language object
     *
     * @return boolean
     */
    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        return !$entity->getIsDefault();
    }

    /**
     * Remove language entity
     *
     * @param \XLite\Model\Language $entity Language object
     *
     * @return boolean
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        return $this->isAllowEntityRemove($entity) && parent::removeEntity($entity);
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Zone::P_ORDER_BY} = ['z.zone_name', 'ASC'];

        return $result;
    }

    /**
     * @return string
     */
    protected function getEditLink()
    {
        return 'zones';
    }

    /**
     * Add right actions
     *
     * @return array
     */
    protected function getRightActions()
    {
        return array_merge(
            parent::getRightActions(),
            ['items_list/model/table/zones/action.tooltip.twig']
        );
    }
}
