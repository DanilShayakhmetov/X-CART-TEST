<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-capability
 *
 * @property string                                                                 capability
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\IntegrationDetails api_integration_preference
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\BillingAgreement   billing_agreement
 */
class Capability extends PayPalModel
{
    /**
     * @return string
     */
    public function getCapability()
    {
        return $this->capability;
    }

    /**
     * @param string $capability
     *
     * @return Capability
     */
    public function setCapability($capability)
    {
        $this->capability = $capability;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\IntegrationDetails
     */
    public function getApiIntegrationPreference()
    {
        return $this->api_integration_preference;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\IntegrationDetails $api_integration_preference
     *
     * @return Capability
     */
    public function setApiIntegrationPreference($api_integration_preference)
    {
        $this->api_integration_preference = $api_integration_preference;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\BillingAgreement
     */
    public function getBillingAgreement()
    {
        return $this->billing_agreement;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\BillingAgreement $billing_agreement
     *
     * @return Capability
     */
    public function setBillingAgreement($billing_agreement)
    {
        $this->billing_agreement = $billing_agreement;

        return $this;
    }
}
