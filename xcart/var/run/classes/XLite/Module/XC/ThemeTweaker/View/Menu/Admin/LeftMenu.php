<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\Menu\Admin;

/**
 * Left menu widget
 */
abstract class LeftMenu extends \XLite\Module\XC\UPS\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    /**
     * Define items
     *
     * @return array
     */
    protected function defineBottomItems()
    {
        $items = parent::defineBottomItems();

        if (isset($items['css_js'][static::ITEM_CHILDREN]) && is_array($items['css_js'][static::ITEM_CHILDREN])) {
            $items['css_js'][static::ITEM_CHILDREN] = array_merge($items['css_js'][static::ITEM_CHILDREN], [
                'theme_tweaker_templates' => [
                    static::ITEM_TITLE      => static::t('Edited templates'),
                    static::ITEM_TARGET     => 'theme_tweaker_templates',
                    static::ITEM_WEIGHT     => 300,
                ],
                'custom_css' => [
                    static::ITEM_TITLE      => static::t('Custom CSS & JS'),
                    static::ITEM_TARGET     => 'custom_css',
                    static::ITEM_WEIGHT     => 400,
                ],
            ]);
        }

        return $items;
    }
}
