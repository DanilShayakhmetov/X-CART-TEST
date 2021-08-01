<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomProductTabs\View\ItemsList\Model;

use XLite\Core\Database;
use XLite\Model\Product\GlobalTab;
use XLite\Model\Product\GlobalTabProvider;

/**
 * GlobalTabs items list
 */
class GlobalTabs extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/CustomProductTabs/global_tabs/style.less';

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
            'enabled' => [
                static::COLUMN_CLASS => 'XLite\View\FormField\Inline\Input\Checkbox\Switcher\ShowHide',
            ],
            'name'    => [
                static::COLUMN_NAME => \XLite\Core\Translation::lbl('Name'),
                static::COLUMN_MAIN => true,
                static::COLUMN_LINK => true,
                static::COLUMN_TEMPLATE => 'modules/XC/CustomProductTabs/product_tabs/parts/name.twig',
            ],
        ];
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Product\GlobalTab::SEARCH_BY_ENABLED_MODULES} = true;

        return $result;
    }

    protected function getRightActions()
    {
        return array_merge(parent::getRightActions(), [
            'modules/XC/CustomProductTabs/global_tabs/help.twig'
        ]);
    }

    /**
     * Return true if 'Edit' link should be displayed in column line
     *
     * @param array                $column
     * @param \XLite\Model\AEntity $entity
     *
     * @return boolean
     */
    protected function isLink(array $column, \XLite\Model\AEntity $entity)
    {
        return parent::isLink($column, $entity) && $entity->getCustomTab();
    }

    /**
     * Check if tab global
     *
     * @param \XLite\Module\XC\CustomProductTabs\Model\Product\GlobalTab $entity Entity
     *
     * @return bool
     */
    protected function isGlobal($entity)
    {
        return true;
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
        return \XLite\Core\Converter::buildURL(
            'global_tab',
            '',
            ['tab_id' => $entity->getId()]
        );
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Product\GlobalTab';
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
        return \XLite\Core\Converter::buildURL(
            'global_tab',
            '',
            ['tab_id' => 0]
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
        return parent::isAllowEntityRemove($entity) && $entity->getCustomTab();
    }

    /**
     * @param array                $column
     * @param \XLite\Model\AEntity $model
     *
     * @return null|string
     */
    public function getHelpText(array $column, \XLite\Model\AEntity $model)
    {
        if (
            $column['code'] === 'actions right'
            && !$model->getCustomTab()
        ) {
            return $this->getTabHelpText($model);
        }

        return null;
    }

    /**
     * @param \XLite\Module\XC\CustomProductTabs\Model\Product\GlobalTab $model
     *
     * @return string
     */
    protected function getTabHelpText(GlobalTab $model)
    {
        if ($model->getServiceName() === 'Description') {
            return static::t('Tab displaying the product\'s detailed description. Added by the X-Cart core');
        }

        if ($model->getServiceName() === 'Specification') {
            return static::t('Tab displaying the product\'s attributes and other details. Added by the X-Cart core');
        }

        if ($model->getServiceName() === 'Comments') {
            return static::t('Tab displaying comments about the product. Added by the addons VK/GoSocial/Disqus', [
                'modules' => implode(', ', $this->getTabModulesList($model))
            ]);
        }

        if (!is_null($model->getServiceName())) {
            return static::t('Added by modules', [
                'modules' => implode(', ', $this->getTabModulesList($model))
            ]);
        }

        return '';
    }

    protected function getTabModulesList(GlobalTab $tab)
    {
        return array_filter(array_map(function (GlobalTabProvider $provider) {
            $code = $provider->getCode();

            if ($code === \XLite\Model\Product\GlobalTabProvider::PROVIDER_CORE) {
                return 'X-Cart core';
            }

            $module = \Includes\Utils\Module\Manager::getRegistry()->getModule($code);
            if ($module && $module->isEnabled()) {
                $moduleName = $module->moduleName;

                return sprintf(
                    '<a href="%s">%s</a>',
                    \Includes\Utils\Module\Manager::getRegistry()->getModuleServiceURL($code),
                    $moduleName
                );
            }

            return false;

        }, $tab->getProviders()->toArray()));
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
        return parent::getContainerClass() . ' global-tabs';
    }

    /**
     * Get panel class
     *
     * @return string|\XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\XC\CustomProductTabs\View\StickyPanel\ItemsList\GlobalTab';
    }
}