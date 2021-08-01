<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-billing_agreement
 *
 * @property string                                                                           description
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\BillingExperiencePreferences billing_experience_preference
 * @property string                                                                           merchant_custom_data
 * @property string                                                                           approval_url
 * @property string                                                                           ec_token
 */
class BillingAgreement extends PayPalModel
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return BillingAgreement
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\BillingExperiencePreferences
     */
    public function getBillingExperiencePreference()
    {
        return $this->billing_experience_preference;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\BillingExperiencePreferences $billing_experience_preference
     *
     * @return BillingAgreement
     */
    public function setBillingExperiencePreference($billing_experience_preference)
    {
        $this->billing_experience_preference = $billing_experience_preference;

        return $this;
    }

    /**
     * @return string
     */
    public function getMerchantCustomData()
    {
        return $this->merchant_custom_data;
    }

    /**
     * @param string $merchant_custom_data
     *
     * @return BillingAgreement
     */
    public function setMerchantCustomData($merchant_custom_data)
    {
        $this->merchant_custom_data = $merchant_custom_data;

        return $this;
    }

    /**
     * @return string
     */
    public function getApprovalUrl()
    {
        return $this->approval_url;
    }

    /**
     * @param string $approval_url
     *
     * @return BillingAgreement
     */
    public function setApprovalUrl($approval_url)
    {
        $this->approval_url = $approval_url;

        return $this;
    }

    /**
     * @return string
     */
    public function getEcToken()
    {
        return $this->ec_token;
    }

    /**
     * @param string $ec_token
     *
     * @return BillingAgreement
     */
    public function setEcToken($ec_token)
    {
        $this->ec_token = $ec_token;

        return $this;
    }
}
