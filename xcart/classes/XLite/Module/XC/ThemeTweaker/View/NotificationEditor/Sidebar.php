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
 * @ListChild (list="admin.main.page.content.center", zone="admin")
 */
class Sidebar extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/notification_editor/sidebar/body.twig';
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'modules/XC/ThemeTweaker/notification_editor/style.css',
        ]);
    }


    public static function getAllowedTargets()
    {
        return [
            'notification_editor',
        ];
    }
}