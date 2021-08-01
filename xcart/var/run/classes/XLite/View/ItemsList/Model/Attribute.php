<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * Attributes items list
 */
class Attribute extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Widget param names
     */
    const PARAM_GROUP = 'group';

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'name' => [
                static::COLUMN_NAME      => $this->getAttributeGroup()
                    ? $this->getAttributeGroup()->getName()
                    : \XLite\Core\Translation::lbl('No group'),
                static::COLUMN_SUBHEADER => $this->getAttributeGroup()
                    ? static::t(
                        'X attributes in group',
                        array(
                            'count' => $this->getAttributeGroup()->getAttributesCount()
                        )
                    )
                    : null,
                static::COLUMN_CLASS     => \XLite\View\FormField\Inline\Input\Text::class,
                static::COLUMN_PARAMS    => ['required' => true],
                static::COLUMN_NO_WRAP   => true,
                static::COLUMN_ORDERBY   => 100,
                static::COLUMN_LINK      => 'attribute',
            ],
            'type' => [
                static::COLUMN_TEMPLATE => 'attributes/parts/type.twig',
                static::COLUMN_ORDERBY  => 200,
            ],
            'displayAbove' =>[
                static::COLUMN_CLASS   => 'XLite\View\FormField\Inline\Input\Checkbox\Switcher\Attribute\DisplayAbove',
                static::COLUMN_ORDERBY => 300,
                static::COLUMN_NAME    => static::t('Display option above the price'),
            ],
            'displayMode' =>[
                static::COLUMN_NAME      => static::t('Display as'),
                static::COLUMN_HEAD_HELP => static::t('This option applies only to attributes with multiple values'),
                static::COLUMN_CLASS    => \XLite\View\FormField\Inline\Select\AttributeDisplayMode::class,
                static::COLUMN_ORDERBY  => 400,
            ],
        );
    }

    /**
     * @param array                                       $column
     * @param \XLite\Model\Attribute|\XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isClassColumnVisible(array $column, \XLite\Model\AEntity $entity)
    {
        $result = parent::isClassColumnVisible($column, $entity);
        if ($column[self::COLUMN_CODE] === 'displayMode') {
            $result = $entity->getType() === \XLite\Model\Attribute::TYPE_SELECT;
        }

        if ($column[self::COLUMN_CODE] === 'displayAbove') {
            $result = $entity->getType() !== \XLite\Model\Attribute::TYPE_HIDDEN;
        }

        return $result;
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Attribute';
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl('attribute');
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New attribute';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_GROUP => new \XLite\Model\WidgetParam\TypeObject(
                'Group', null, false, '\XLite\Model\AttributeGroup'
            ),
        );
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\Infinity';
    }

    /**
     * Get attribute group
     *
     * @return \XLite\Model\AttributeGroup
     */
    protected function getAttributeGroup()
    {
        return $this->getParam(static::PARAM_GROUP);
    }

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_MOVE;
    }

    /**
     * Check if there are any results to display in list
     *
     * @return boolean
     */
    protected function hasResults()
    {
        return true;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return $this->getAttributeGroup()
            || 0 < $this->getItemsCount();
    }

    /**
     * Check - pager box is visible or not
     *
     * @return boolean
     */
    protected function isPagerVisible()
    {
        return false;
    }

    /**
     * Build entity page URL
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function buildEntityURL(\XLite\Model\AEntity $entity, array $column)
    {
        return 'javascript: void(0);';
    }

    /**
     * @return string
     */
    protected function getEditLink()
    {
        return true;
    }

    /**
     * Get edit link params string
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return string
     */
    protected function getEditLinkAttributes(\XLite\Model\AEntity $entity, array $column)
    {
        $params = array();
        $params[] = 'data-id=' . $entity->getId();

        if ($entity->getProductClass()) {
            $params[] = 'data-class-id=' . $entity->getProductClass()->getId();
        }

        return parent::getEditLinkAttributes($entity, $column) . implode(' ', $params);
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        $class = parent::getContainerClass() . ' attributes';

        if ($this->getAttributeGroup()) {
            $class .= ' group';
        }

        return $class;
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return null;
    }


    // {{{ Search

    /**
     * Return search parameters.
     *
     * @return array
     */
    static public function getSearchParams()
    {
        return array();
    }

    /**
     * Return params list to use for search
     * TODO refactor
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $paramValue = $this->getParam($requestParam);

            if ('' !== $paramValue && 0 !== $paramValue) {
                $result->$modelParam = $paramValue;
            }
        }

        $result->productClass = $this->getProductClass();
        if (\XLite\Core\Request::getInstance()->isGet()) {
            $result->attributeGroup = $this->getAttributeGroup();
            $result->productClass = $this->getProductClass();
        }
        $result->product = null;

        return $result;
    }

    // }}}
}
