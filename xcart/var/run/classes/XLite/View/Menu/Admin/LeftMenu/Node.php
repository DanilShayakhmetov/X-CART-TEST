<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LeftMenu;

/**
 * Node
 */
class Node extends \XLite\View\Menu\Admin\LeftMenu\ANode
{
    protected function isCacheAvailable()
    {
        return true;
    }

    protected function getCacheParameters()
    {
        return array_merge(
            parent::getCacheParameters(),
            [
                get_class($this),
                $this->getWidgetParams()
            ]
        );
    }
}
