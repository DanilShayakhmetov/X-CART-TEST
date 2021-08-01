<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;


/**
 * Notification details page
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Notification extends \XLite\View\Tabs\WithCustomParam
{
    public static function getAllowedTargets()
    {
        return [
            'notification',
        ];
    }

    protected function defineUrlParam()
    {
        return 'page';
    }

    protected function defineTabs()
    {
        $list = [];

        $notification = $this->getNotification();

        if ($notification->getAvailableForCustomer() || $notification->getEnabledForCustomer()) {
            $list['customer'] = [
                'weight'   => 100,
                'title'    => static::t('notification.tab.customer'),
                'template' => 'notification/body.twig',
            ];
        }

        if ($notification->getAvailableForAdmin() || $notification->getEnabledForAdmin()) {
            $list['admin'] = [
                'weight'   => 200,
                'title'    => static::t('notification.tab.administrator'),
                'template' => 'notification/body.twig',
            ];
        }

        return $list;
    }

    protected function buildTabURL($param)
    {
        return $this->buildURL('notification', '', [
            'templatesDirectory' => $this->getNotification()->getTemplatesDirectory(),
            'page' => $param
        ]);
    }

    /**
     * @return \XLite\Model\Notification
     */
    protected function getNotification()
    {
        return \XLite::getController()->getNotification();
    }
}