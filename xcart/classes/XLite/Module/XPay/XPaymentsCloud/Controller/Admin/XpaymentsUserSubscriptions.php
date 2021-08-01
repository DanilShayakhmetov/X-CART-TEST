<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Controller\Admin;

use XLite\Model\Profile;

/**
 * Subscriptions list for user controller
 */
class XpaymentsUserSubscriptions extends XpaymentsSubscriptions
{
    /**
     * getProfileId
     *
     * @return integer
     */
    public function getProfileId()
    {
        return \XLite\Core\Request::getInstance()->profile_id;
    }

    /**
     * Get customer profile
     *
     * @return Profile
     */
    protected function getCustomerProfile()
    {
        $profileId = \XLite\Core\Request::getInstance()->profile_id;
        if (empty($profileId)) {
            $profileId = \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();
        }

        /** @var Profile $profile */
        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')
                                       ->find(intval($profileId));

        return $profile;
    }

    /**
     * Get profile
     *
     * @return Profile
     */
    public function getProfile()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Profile')
            ->find($this->getProfileId());
    }

}
