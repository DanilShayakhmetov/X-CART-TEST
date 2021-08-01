<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-integration_details
 *
 * @property string                                                                    partner_id
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\RestApiIntegration    rest_api_integration
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\RestThirdPartyDetails rest_third_party_details
 */
class IntegrationDetails extends PayPalModel
{
    /**
     * @return string
     */
    public function getPartnerId()
    {
        return $this->partner_id;
    }

    /**
     * @param string $partner_id
     *
     * @return IntegrationDetails
     */
    public function setPartnerId($partner_id)
    {
        $this->partner_id = $partner_id;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\RestApiIntegration
     */
    public function getRestApiIntegration()
    {
        return $this->rest_api_integration;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\RestApiIntegration $rest_api_integration
     *
     * @return IntegrationDetails
     */
    public function setRestApiIntegration($rest_api_integration)
    {
        $this->rest_api_integration = $rest_api_integration;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\RestThirdPartyDetails
     */
    public function getRestThirdPartyDetails()
    {
        return $this->rest_third_party_details;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\RestThirdPartyDetails $rest_third_party_details
     *
     * @return IntegrationDetails
     */
    public function setRestThirdPartyDetails($rest_third_party_details)
    {
        $this->rest_third_party_details = $rest_third_party_details;

        return $this;
    }
}
