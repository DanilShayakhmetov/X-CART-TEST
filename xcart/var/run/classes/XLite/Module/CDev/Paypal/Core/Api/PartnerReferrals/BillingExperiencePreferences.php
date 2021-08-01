<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-billing_experience_preference
 *
 * @property string experience_id
 * @property bool   billing_context_set
 */
class BillingExperiencePreferences extends PayPalModel
{
    /**
     * @return string
     */
    public function getExperienceId()
    {
        return $this->experience_id;
    }

    /**
     * @param string $experience_id
     *
     * @return BillingExperiencePreferences
     */
    public function setExperienceId($experience_id)
    {
        $this->experience_id = $experience_id;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBillingContextSet()
    {
        return $this->billing_context_set;
    }

    /**
     * @param bool $billing_context_set
     *
     * @return BillingExperiencePreferences
     */
    public function setBillingContextSet($billing_context_set)
    {
        $this->billing_context_set = $billing_context_set;

        return $this;
    }
}
