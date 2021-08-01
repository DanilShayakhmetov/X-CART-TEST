<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\QuickMenu;

/**
 * Quick menu widget
 */
class Menu extends \XLite\View\Menu\Admin\AAdmin
{
    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'menu/quick_menu';
    }

    /**
     * Get default widget
     *
     * @return string
     */
    protected function getDefaultWidget()
    {
        return 'XLite\View\Menu\Admin\QuickMenu\Node';
    }

    /**
     * Define menu items
     *
     * @return array
     */
    protected function defineItems()
    {
        $result = [
            'add_product'  => [
                static::ITEM_TITLE      => static::t('Product'),
                static::ITEM_ICON_SVG   => 'images/add_product.svg',
                static::ITEM_TARGET     => 'product',
                static::ITEM_PERMISSION => 'manage catalog',
                static::ITEM_WEIGHT     => 100,
            ],
            'add_category' => [
                static::ITEM_TITLE      => static::t('Category'),
                static::ITEM_ICON_SVG   => 'images/add_category.svg',
                static::ITEM_TARGET     => 'categories',
                static::ITEM_PERMISSION => 'manage catalog',
                static::ITEM_EXTRA      => ['add_new' => 1],
                static::ITEM_WEIGHT     => 200,
            ],
            'add_user'     => [
                static::ITEM_TITLE      => static::t('User'),
                static::ITEM_ICON_SVG   => 'images/add_user.svg',
                static::ITEM_TARGET     => 'profile',
                static::ITEM_EXTRA      => ['mode' => 'register'],
                static::ITEM_PERMISSION => 'manage users',
                static::ITEM_WEIGHT     => 300,
            ],
        ];

        if (\XLite::isFreeLicense()) {
            $result['add_coupon'] = [
                static::ITEM_TITLE      => static::t('Coupon'),
                static::ITEM_ICON_SVG   => 'images/add_product.svg',
                static::ITEM_TARGET     => 'main',
                static::ITEM_EXTRA      => ['page' => 'license_restriction'],
                static::ITEM_WEIGHT     => 400,
            ];
        }

        return $result;
    }
}
