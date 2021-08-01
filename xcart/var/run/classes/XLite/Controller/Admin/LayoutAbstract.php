<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

use Xlite\Core\Request;
use XLite\Core\Skin;
use XLite\Rebuild\Connector;

/**
 * Layout
 */
abstract class LayoutAbstract extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        $list = parent::defineFreeFormIdActions();
        $list[] = 'change_layout';

        return $list;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Layout');
    }

    /**
     * Returns templates store URL
     *
     * @return string
     */
    public function getTemplatesStoreURL()
    {
        return \XLite::getXCartURL('https://market.x-cart.com/ecommerce-templates/');
    }

    /**
     * Returns templates store URL
     *
     * @return string
     */
    public function getFreeQuoteURL()
    {
        return \XLite::getXCartURL('https://www.x-cart.com/contact-us.html?reason=subj_2');
    }

    /**
     * If it need to show free quote for design block
     *
     * @return bool
     */
    public function showFreeQuoteBlock()
    {
        return true;
    }

    protected function doNoAction()
    {
        if (\XLite\Core\Request::getInstance()->rebuildId) {
            \XLite\Core\TopMessage::addInfo(
                'If anything crops up, just rollback or contact our support team - they know how to fix it right away.',
                [
                    'rollback_url' => \XLite::getInstance()->getShopURL('service.php?/rollback', null, ['id' => \XLite\Core\Request::getInstance()->rebuildId])
                ]
            );

            $this->setReturnURL($this->buildURL('layout'));
        }
    }

    /**
     * Change template
     *
     * @return void
     * @throws \Exception
     */
    protected function doActionChangeTemplate()
    {
        list ($moduleId, $color) = $this->parseRequestParam(Request::getInstance()->template);

        if ($color) {
            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
                [
                    'category' => 'Layout',
                    'name' => 'color',
                    'value' => $color,
                ]
            );
        }

        if ($moduleId && $moduleId !== Skin::getInstance()->getCurrentSkinModuleId()) {
            if (Connector::enableSkin($moduleId, $this->buildFullURL('layout'))) {
                \XLite\Core\TopMessage::addInfo(
                    'Please, re-generate the product catalog images to apply the new template settings.',
                    [
                        'images_page_url' => $this->buildURL('images'),
                    ]
                );
            }
        }
    }

    /**
     * Return switch data
     *
     * @param $value
     * @return array
     */
    protected function parseRequestParam($value)
    {
        if (Skin::SKIN_STANDARD !== $value) {
            return (false !== strpos($value, '_'))
                ? explode('_', $value)
                : [$value, null];
        }

        return [Skin::SKIN_STANDARD, 'Default'];
    }

    /**
     * Change layout
     *
     * @return void
     */
    protected function doActionChangeLayout()
    {
        $layoutType = Request::getInstance()->layout_type;
        $layoutGroup = Request::getInstance()->layout_group ?: \XLite\Core\Layout::LAYOUT_GROUP_DEFAULT;

        $availableLayoutTypes = \XLite\Core\Layout::getInstance()->getAvailableLayoutTypes();
        $groupAvailableTypes = isset($availableLayoutTypes[$layoutGroup])
            ? $availableLayoutTypes[$layoutGroup]
            : [];

        if (in_array($layoutType, $groupAvailableTypes, true)) {
            $group_suffix = ($layoutGroup == \XLite\Core\Layout::LAYOUT_GROUP_DEFAULT ? '' : '_' . $layoutGroup);

            \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
                'category' => 'Layout',
                'name' => 'layout_type' . $group_suffix,
                'value' => $layoutType,
            ]);
        }

        \XLite\Core\TopMessage::addInfo(
            'Layout has been changed. Review the updated storefront.',
            [
                'storefront' => $this->getShopURL('')
            ]
        );

        $this->setReturnURL($this->buildURL('layout'));
    }

    // {{{ Layout types

    /**
     * Returns available layout types
     *
     * @return array
     */
    public function getLayoutTypes()
    {
        return \XLite\Core\Layout::getInstance()->getAvailableLayoutTypes();
    }

    /**
     * Returns current layout types
     *
     * @return string
     */
    public function getLayoutType()
    {
        return \XLite\Core\Layout::getInstance()->getLayoutType();
    }
}
