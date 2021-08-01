<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\ItemsList\Model;

use XLite\Core\Templating\CacheManagerInterface;
use XLite\Module\XC\ThemeTweaker\Core\Layout;

/**
 * Theme tweaker templates items list
 */
class Template extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/ThemeTweaker/theme_tweaker_templates/style.css';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/ThemeTweaker/theme_tweaker_templates/controller.js';

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
            'template' => [
                static::COLUMN_NAME    => static::t('Template'),
                static::COLUMN_MAIN    => true,
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_LINK    => 'theme_tweaker_template',
                static::COLUMN_ORDERBY => 100,
            ],
            'date' => [
                static::COLUMN_NAME     => static::t('Date'),
                static::COLUMN_TEMPLATE => 'modules/XC/ThemeTweaker/theme_tweaker_templates/parts/cell.date.twig',
                static::COLUMN_NO_WRAP  => false,
                static::COLUMN_ORDERBY  => 200,
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
        return 'XLite\Module\XC\ThemeTweaker\Model\Template';
    }

    // {{{ Behaviors

    protected function isSwitchable()
    {
        return true;
    }

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return true;
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' theme_tweaker_templates';
    }

    /**
     * Returns full path
     *
     * @param string $shortPath Short path
     * @param string $skin      Skin OPTIONAL
     *
     * @return string
     */
    protected function getFullPathByShortPath($shortPath, $skin = Layout::THEME_TWEAKER_CUSTOMER_INTERFACE)
    {
        $result = '';

        /** @var \XLite\Core\Layout $layout */
        $layout = \XLite\Core\Layout::getInstance();

        foreach ($layout->getSkinPaths(\XLite::CUSTOMER_INTERFACE) as $path) {
            if ($path['name'] == $skin) {
                $result = $path['fs'] . LC_DS . $shortPath;

                break;
            }
        }

        return $result;
    }

    /**
     * Returns a (cached) templating engine instance
     *
     * @return CacheManagerInterface
     */
    protected function getTemplateCacheManager()
    {
        return $this->getContainer()->get('template_cache_manager');
    }

    /**
     * Remove entity
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return boolean
     */
    protected function removeEntity(\XLite\Model\AEntity $entity)
    {
        /** @var Layout $layout */
        $layout = \XLite\Core\Layout::getInstance();

        $localPath = $entity->getTemplate();
        $interfaceByPath = $layout->getInterfaceByLocalPath($localPath);

        if ($interfaceByPath === \XLite::MAIL_INTERFACE) {
            $innerInterface = $layout->getInnerInterfaceByLocalPath($localPath);
            $layout->setMailSkin($innerInterface);
        }

        $fullPath = $layout->getFullPathByLocalPath($localPath, $interfaceByPath);

        \Includes\Utils\FileManager::deleteFile($fullPath);
        \Includes\Utils\FileManager::deleteFile("{$fullPath}.tmp");

        $this->getTemplateCacheManager()->invalidate($fullPath);

        parent::removeEntity($entity);

        return true;
    }

    protected function updateEntities()
    {
        foreach ($this->getPageDataForUpdate() as $entity) {
            $entity->getRepository()->update($entity, [], false);
            if ($this->isDefault()) {
                $this->setDefaultValue($entity, $this->isDefaultEntity($entity));
            }

            $this->renameTemplate($entity);
        }
    }

    /**
     * @param \XLite\Model\AEntity $entity Entity
     */
    public function renameTemplate(\XLite\Model\AEntity $entity)
    {
        /** @var Layout $layout */
        $layout = \XLite\Core\Layout::getInstance();

        $localPath = $entity->getTemplate();
        $interfaceByPath = $layout->getInterfaceByLocalPath($localPath);

        if ($interfaceByPath === \XLite::MAIL_INTERFACE) {
            $innerInterface = $layout->getInnerInterfaceByLocalPath($localPath);
            $layout->setMailSkin($innerInterface);
        }

        $fullPath = $layout->getFullPathByLocalPath($localPath, $interfaceByPath);
        $tmpFullPath = "{$fullPath}.tmp";

        if ($entity->getEnabled()) {
            \Includes\Utils\FileManager::move($tmpFullPath, $fullPath);
        } else {
            \Includes\Utils\FileManager::move($fullPath, $tmpFullPath);
        }

        $this->getTemplateCacheManager()->invalidate($fullPath);
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Zone::P_ORDER_BY} = ['t.date', 'DESC'];

        return $result;
    }

    /**
     * Description for blank items list
     *
     * @return string
     */
    protected function getEmptyListDescription()
    {
        return static::t('itemslist.admin.template.blank');
    }

    /**
     * Get panel class
     *
     * @return string
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\XC\ThemeTweaker\View\StickyPanel\TemplatesForm';
    }
}
