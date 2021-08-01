<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\View\ItemsList\Model\Product;

/**
 * Product tabs items list
 */
class Tab extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'enabled' => [
                static::COLUMN_CLASS => 'XLite\View\FormField\Inline\Input\Checkbox\Switcher\ShowHide',
            ],
            'name'    => [
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_MAIN     => true,
                static::COLUMN_LINK     => true,
                static::COLUMN_TEMPLATE => 'modules/XC/CustomProductTabs/product_tabs/parts/name.twig',
            ],
        ];
    }

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/CustomProductTabs/product_tabs/style.css';

        return $list;
    }

    /**
     * Check if tab global
     *
     * @param \XLite\Module\XC\CustomProductTabs\Model\Product\Tab $entity Entity
     *
     * @return bool
     */
    protected function isGlobal($entity)
    {
        return $entity && $entity->isGlobal();
    }

    /**
     * Return true if 'Edit' link should be displayed in column line
     *
     * @param \XLite\Model\AEntity $entity Entity
     * @param array                $column Column data
     *
     * @return boolean
     */
    protected function isLink(array $column, \XLite\Model\AEntity $entity)
    {
        return parent::isLink($column, $entity) && !$entity->isGlobalStatic();
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
        if ($entity->isGlobalCustom()) {
            $url = \XLite\Core\Converter::buildURL(
                'product',
                '',
                [
                    'global_tab_id' => $entity->getGlobalTab()->getId(),
                    'product_id' => \XLite\Core\Request::getInstance()->product_id,
                    'page'       => 'tabs',
                ]
            );
        } else {
            $url = \XLite\Core\Converter::buildURL(
                'product',
                null,
                [
                    'product_id' => \XLite\Core\Request::getInstance()->product_id,
                    'page'       => 'tabs',
                    'tab_id'     => $entity->getId(),
                ]
            );
        }

        return $url;
    }

    /**
     * Define line class as list of names
     *
     * @param integer              $index  Line index
     * @param \XLite\Model\AEntity $entity Line model OPTIONAL
     *
     * @return array
     */
    protected function defineLineClass($index, \XLite\Model\AEntity $entity = null)
    {
        $classes = parent::defineLineClass($index, $entity);

        if ($entity && $entity->isGlobal()) {
            $classes[] = 'global-tab';
        }

        if ($entity && $entity->isGlobalCustom()) {
            $classes[] = 'edit-allowed';
        }

        return $classes;
    }

    /**
     * Get label for 'Edit' link
     *
     * @param \XLite\Module\XC\CustomProductTabs\Model\Product\Tab $entity Entity
     *
     * @return string
     */
    protected function getEditLinkLabel($entity)
    {
        return $entity->isGlobal() ? static::t('Edit Globally') : static::t('Edit');
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Module\XC\CustomProductTabs\Model\Product\Tab';
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
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return \XLite\Core\Converter::buildUrl(
            'product',
            '',
            [
                'page'       => 'tabs',
                'tab_id'     => 0,
                'product_id' => \XLite\Core\Request::getInstance()->product_id
            ]
        );
    }

    /**
     * Get create button label
     *
     * @return string
     */
    protected function getCreateButtonLabel()
    {
        return 'New tab';
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
     * Check - remove entity or not
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function isAllowEntityRemove(\XLite\Model\AEntity $entity)
    {
        return parent::isAllowEntityRemove($entity) && !$entity->isGlobal();
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

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' product_tabs';
    }

    /**
     * Get panel class
     *
     * @return string
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\XC\CustomProductTabs\View\StickyPanel\ItemsList\Product\Tab';
    }

    /**
     * Get top actions
     *
     * @return array
     */
    protected function getTopActions()
    {
        $actions = parent::getTopActions();

        $actions[] = 'modules/XC/CustomProductTabs/product_tabs/manage_global_tabs.twig';

        return $actions;
    }

    /**
     * Returns global tabs link
     *
     * @return string
     */
    public function getGlobalTabsLink()
    {
        return $this->buildURL('global_tabs');
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();
        $this->commonParams['product_id'] = $this->getProductId();

        return $this->commonParams;
    }

    // {{{ Search

    /**
     * Return search parameters.
     *
     * @return array
     */
    static public function getSearchParams()
    {
        return [];
    }

    /**
     * Return params list to use for search
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

        $result->product = $this->getProduct();

        return $result;
    }

    // }}}
}
