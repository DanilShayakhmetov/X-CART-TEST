<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Form;

/**
 * X-Payments Saved cards form 
 */
class XpaymentsCards extends \XLite\View\Form\AForm
{
    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'xpayments_cards';
    }

    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update_default_card';
    }

    /**
     * Get customer profile id
     *
     * @return integer
     */
    protected function getCustomerProfileId()
    {
        if (\XLite::isAdminZone()) {
            $profileId = \XLite\Core\Request::getInstance()->profile_id;
        }
        if (empty($profileId)) {
            $profileId = \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();
        }
        return $profileId;
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $params = [
            'card_id' => 0,
        ];

        if (\XLite::isAdminZone()) {
            $params['profile_id'] = $this->getCustomerProfileId();
        };

        return $params;

    }
}
