<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View\Menu\Admin\Info;

/**
 * Left side menu widget
 */
class Menu extends \XLite\View\Menu\Admin\Info\Menu implements \XLite\Base\IDecorator
{
    /**
     * Define menu items
     *
     * @return array
     */
    protected function defineItems()
    {
        $items = parent::defineItems();

        $items['CloudSearchTrialNotice'] = [
            static::ITEM_WEIGHT => 1000,
            static::ITEM_WIDGET => 'XLite\Module\QSL\CloudSearch\View\Menu\Admin\Info\Node\TrialNotice',
        ];

        return $items;
    }
}
