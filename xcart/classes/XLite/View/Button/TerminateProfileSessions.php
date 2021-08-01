<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;
use XLite\Core\Auth;

/**
 * 'Terminate profile sessions' button widget
 */
class TerminateProfileSessions extends \XLite\View\Button\Link
{
    /**
     * Get default CSS class name
     *
     * @return string
     */
    protected function getDefaultStyle()
    {
        return 'action terminate-sessions always-enabled';
    }

    /**
     * Get default label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Logout this user';
    }

    /**
     * We make the full location path for the provided URL
     *
     * @return string
     */
    protected function getLocationURL()
    {
        return $this->buildURL('profile', 'terminate_sessions', array(
            'profile_id' => $this->getProfile()->getProfileId()
        ));
    }

    /**
     * Get profile
     *
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile')->find(
            \XLite\Core\Request::getInstance()->profile_id
        ) ?: Auth::getInstance()->getProfile();
    }

    /**
     * Return true if button is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
               && $this->isProfileAllowed();
    }

    /**
     * Return true if profile meets conditions
     *
     * @return boolean
     */
    protected function isProfileAllowed()
    {
        return $this->getProfile()
               && $this->getProfile()->isPersistent()
               && !$this->getProfile()->getAnonymous()
               && (
                   $this->getProfile()->getProfileId() !== Auth::getInstance()->getProfile()->getProfileId()
                   || Auth::getInstance()->isPermissionAllowed('manage users')
                   || Auth::getInstance()->isPermissionAllowed('manage admins')
               );
    }
}
