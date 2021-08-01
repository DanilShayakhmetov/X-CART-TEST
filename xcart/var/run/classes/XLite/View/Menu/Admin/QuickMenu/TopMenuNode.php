<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\QuickMenu;

/**
 * Quick menu top menu node
 *
 * @ListChild (list="admin.main.page.header", weight="400", zone="admin")
 */
class TopMenuNode extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'menu/quick_menu/top_menu_node.twig';
    }

    /**
     * @return boolean
     */
    protected function isVisible()
    {
        return false;
    }
}
