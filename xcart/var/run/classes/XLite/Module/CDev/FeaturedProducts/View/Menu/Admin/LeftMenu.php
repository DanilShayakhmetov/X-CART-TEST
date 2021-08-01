<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\View\Menu\Admin;

use XLite\Core\Auth;
use XLite\Model\Role\Permission;

 class LeftMenu extends \XLite\Module\CDev\FedEx\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    protected function defineItems()
    {
        $items = parent::defineItems();

        if (!Auth::getInstance()->isPermissionAllowed(Permission::ROOT_ACCESS) && !Auth::getInstance()->isPermissionAllowed('manage front page')) {
            $items['content'][static::ITEM_CHILDREN]['featured_products'] = [
                static::ITEM_TITLE      => static::t('Featured products'),
                static::ITEM_TARGET     => 'featured_products',
                static::ITEM_PERMISSION => 'manage catalog',
                static::ITEM_WEIGHT     => 200,
            ];
        }

        return $items;
    }
}