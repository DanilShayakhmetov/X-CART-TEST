<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\Form;


class NotificationEditorSidebarData extends \XLite\View\Form\AForm
{
    protected function getDefaultTarget()
    {
        return 'notification_editor';
    }

    protected function getDefaultAction()
    {
        return 'change_data';
    }

    protected function getDefaultParams()
    {
        $params = [
            'templatesDirectory' => \XLite\Core\Request::getInstance()->templatesDirectory,
            'interface'          => \XLite\Core\Request::getInstance()->interface,
        ];

        return $params;
    }
}