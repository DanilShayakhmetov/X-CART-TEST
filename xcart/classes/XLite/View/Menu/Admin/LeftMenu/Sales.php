<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LeftMenu;

use XLite\Core\View\DynamicWidgetInterface;


/**
 * Sales
 */
class Sales extends \XLite\View\Menu\Admin\LeftMenu\ANode implements DynamicWidgetInterface
{
    protected function getLabel()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Order')->searchRecentOrders(null, true)
            ?: null;
    }

    protected function getCacheParameters()
    {
        return array_merge(parent::getCacheParameters(), [
            \XLite\Core\Database::getRepo('XLite\Model\Order')->getVersion()
        ]);
    }
}