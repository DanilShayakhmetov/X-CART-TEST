<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LeftMenu;

use XLite\Core\Cache\ExecuteCached;
use XLite\Core\View\DynamicWidgetInterface;

/**
 * Sales channels node
 */
class ClonedProducts extends \XLite\View\Menu\Admin\LeftMenu\Node implements DynamicWidgetInterface
{
    public function isVisible()
    {
        return parent::isVisible()
            && $this->hasClonedProducts();
    }

    protected function hasClonedProducts()
    {
        $cacheParams = [
            'menu_hasClonedProducts',
            \XLite\Core\Database::getRepo('XLite\Model\Product')->getVersion()
        ];

        return ExecuteCached::executeCached(function() {
            $cnd                                           = new \XLite\Core\CommonCell();
            $cnd->{\XLite\Model\Repo\Product::P_SUBSTRING} = '[ clone ]';
            $cnd->{\XLite\Model\Repo\Product::P_BY_TITLE}  = 'Y';

            return 0 < \XLite\Core\Database::getRepo('XLite\Model\Product')->search($cnd, true);
        }, $cacheParams);
    }

    protected function getCacheParameters()
    {
        return array_merge(
            parent::getCacheParameters(),
            [
                \XLite\Core\Database::getRepo('XLite\Model\Product')->getVersion()
            ]
        );
    }
}
