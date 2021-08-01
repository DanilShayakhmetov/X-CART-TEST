<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Module\QSL\CloudSearch\Main;

/**
 * Layout manager
 */
 class Layout extends \XLite\Module\QSL\CloudSearch\Core\LayoutCrispWhite implements \XLite\Base\IDecorator
{
    /*
     * Store sidebar content so we can correctly change sidebar state after materializing FiltersBoxPlaceholder -> FiltersBox widget
     */
    protected $cloudSearchSidebarContent;

    /**
     * @return string
     */
    public function getCloudSearchSidebarContent()
    {
        return $this->cloudSearchSidebarContent;
    }

    /**
     * @param string $content
     */
    public function setCloudSearchSidebarContent($content)
    {
        $this->cloudSearchSidebarContent = $content;
    }
}
