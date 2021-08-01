<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\Layout\Customer;

use XLite\Core\Layout;
use XLite\Module\XC\ThemeTweaker\Core\ThemeTweaker;
use XLite\Module\XC\ThemeTweaker\Main;

/**
 * Sidebar first list collection container
 */
class SidebarHidden extends \XLite\View\ListContainer
{
    /**
     * @return bool
     */
    protected function isVisible()
    {
        return !Layout::getInstance()->isSidebarFirstVisible()
            && !Layout::getInstance()->isSidebarSecondVisible()
            && ThemeTweaker::getInstance()->isInLayoutMode();
    }

    /**
     * Define view list item metadata
     *
     * @param \XLite\Model\ViewList $item ViewList item
     *
     * @return array
     */
    protected function getListItemMetadata($item)
    {
        $metadata = parent::getListItemMetadata($item);
        $metadata['visibility'] = false;

        return $metadata;
    }

    /**
     * Return string with list item classes
     *
     * @param \XLite\View\AView $widget     Displaying widget
     *
     * @return string
     */
    protected function getViewListItemClasses($widget)
    {
        $classes = parent::getViewListItemClasses($widget);

        return $classes . ' list-item__sidebar-hidden';
    }

    /**
     * Displays inner content
     */
    public function displayInnerContent()
    {
        $content = parent::displayInnerContent();
        echo $content;
    }

    /**
     * There are two modes of sidebars:
     *   single_sidebar
     *     We are displaying both viewlists in one sidebar
     *   two_sidebar
     *     We are displaying viewlists independently in SidebarFirst and SidebarSecond
     *
     * @return string
     */
    protected function getDefaultInnerList()
    {
        return 'sidebar.first,sidebar.second';
    }
}
