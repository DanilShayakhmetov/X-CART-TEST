<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\NotificationEditor;

/**
 * Sidebar
 *
 * @ListChild (list="body", zone="admin", weight="50")
 */
class Header extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/notification_editor/header.twig';
    }

    public static function getAllowedTargets()
    {
        return [
            'notification_editor',
        ];
    }

    /**
     * @return string
     */
    protected function getNotificationTitle()
    {
        return \XLite::getController()->getNotification()->getName();
    }

    /**
     * @return string
     */
    protected function getBackUrl()
    {
        return $this->buildURL(
            'notification',
            '',
            [
                'templatesDirectory' => \XLite\Core\Request::getInstance()->templatesDirectory,
                'page'               => \XLite\Core\Request::getInstance()->interface,
            ]
        );
    }
}