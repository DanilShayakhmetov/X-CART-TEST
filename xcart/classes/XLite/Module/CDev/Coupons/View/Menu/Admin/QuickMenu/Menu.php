<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\Menu\Admin\QuickMenu;

/**
 * Quick menu widget
 */
class Menu extends \XLite\View\Menu\Admin\QuickMenu\Menu implements \XLite\Base\IDecorator
{
    /**
     * Define quick links
     *
     * @return array
     */
    protected function defineItems()
    {
        $result = parent::defineItems();

        $result['add_coupon'] = [
            static::ITEM_TITLE      => static::t('Coupon'),
            static::ITEM_ICON_SVG   => 'images/add_product.svg',
            static::ITEM_TARGET     => 'coupon',
            static::ITEM_WEIGHT     => 400,
            static::ITEM_PERMISSION => 'manage coupons',
        ];

        return $result;
    }
}
