<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LeftMenu;

use XLite\Core\View\DynamicWidgetInterface;

/**
 * Banner rotation
 */
class BannerRotation extends \XLite\View\Menu\Admin\LeftMenu\Node implements DynamicWidgetInterface
{
    public function isVisible()
    {
        return parent::isVisible()
            && !\XLite\Core\Auth::getInstance()->isPermissionAllowed('manage catalog')
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage banners');
    }

    protected function getCacheParameters()
    {
        return array_merge(
            parent::getCacheParameters(),
            [
                \XLite\Core\Auth::getInstance()->getProfile()->getProfileId()
            ]
        );
    }
}
