<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\NotificationEditor\Sidebar;


class Tree extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/notification_editor/sidebar/tree/body.twig';
    }

    /**
     * @return string
     */
    protected function getTreeContent()
    {
        $viewer = \XLite::getController()->getViewer();
        return $viewer::getHtmlTree();
    }

    protected function getInterface()
    {
        return \XLite::MAIL_INTERFACE;
    }

    protected function getInnerInterface()
    {
        return \XLite\Core\Request::getInstance()->interface;
    }
}