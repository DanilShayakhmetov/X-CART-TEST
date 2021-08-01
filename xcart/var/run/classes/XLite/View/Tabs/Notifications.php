<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to translations section
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Notifications extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'notifications';
        $list[] = 'notification_common';
        $list[] = 'notification_attachments';
        $list[] = 'email_settings';
        $list[] = 'test_email';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'notifications'            => [
                'weight' => 100,
                'title'  => static::t('Settings'),
                'widget' => 'XLite\View\ItemsList\Model\Notification',
            ],
            'notification_common'      => [
                'weight'   => 200,
                'title'    => static::t('Header, greeting & signature'),
                'template' => 'notifications/common.twig',
            ],
            'notification_attachments' => [
                'weight'   => 300,
                'title'    => static::t('Attachments'),
                'template' => 'notifications/attachments.twig',
            ],
            'email_settings'           => [
                'weight' => 400,
                'title'  => static::t('Email transfer settings'),
                'widget' => 'XLite\View\Model\Settings',
            ],
            'test_email'               => [
                'weight' => 500,
                'title'  => static::t('Test email configuration'),
                'widget' => 'XLite\View\TestEmail',
            ],
        ];
    }
}
