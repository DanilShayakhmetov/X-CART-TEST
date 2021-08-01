<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Dashboard\Admin\InfoBlock\Notification;

use XLite\Core\Auth;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;
use XLite\Model\Membership;
use XLite\Model\Profile;

/**
 * @ListChild (list="dashboard.info_block.notifications", weight="600", zone="admin")
 */
class PendingMembership extends \XLite\View\Dashboard\Admin\InfoBlock\ANotification
{
    use ExecuteCachedTrait;

    /**
     * @return string
     */
    protected function getNotificationType()
    {
        return 'pendingMemberships';
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' panding-membership';
    }

    /**
     * @return string
     */
    protected function getHeader()
    {
        return static::t('Pending memberships');
    }

    /**
     * @return string
     */
    protected function getHeaderUrl()
    {
        $memberships = Database::getRepo(Membership::class)->findActiveMemberships();

        $params = [];
        $i      = 0;
        foreach ($memberships as $membership) {
            $key          = "membership[$i]";
            $params[$key] = 'P_' . $membership->getMembershipId();
            $i++;
        }

        return $this->buildURL(
            'profile_list',
            '',
            $params
        );
    }

    /**
     * @return int
     */
    protected function getCounter()
    {
        return $this->executeCachedRuntime(static function () {
            return Database::getRepo(Profile::class)->countPendingMemberships();
        });
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getCounter() > 0;
    }

    /**
     * @return bool
     */
    protected function checkACL()
    {
        return parent::checkACL()
            && (Auth::getInstance()->hasRootAccess()
                || Auth::getInstance()->isPermissionAllowed('manage users'));
    }
}
