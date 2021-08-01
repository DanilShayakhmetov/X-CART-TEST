<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-oauth_integration
 *
 * @property string                                                              integration_type
 * @property string                                                              integration_method
 * @property string                                                              status
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\OAuthThirdParty oauth_third_party
 */
class OAuthIntegration extends PayPalModel
{
    /**
     * @return string
     */
    public function getIntegrationType()
    {
        return $this->integration_type;
    }

    /**
     * @param string $integration_type
     *
     * @return OAuthIntegration
     */
    public function setIntegrationType($integration_type)
    {
        $this->integration_type = $integration_type;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntegrationMethod()
    {
        return $this->integration_method;
    }

    /**
     * @param string $integration_method
     *
     * @return OAuthIntegration
     */
    public function setIntegrationMethod($integration_method)
    {
        $this->integration_method = $integration_method;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return OAuthIntegration
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\OAuthThirdParty
     */
    public function getOauthThirdParty()
    {
        return $this->oauth_third_party;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\OAuthThirdParty $oauth_third_party
     *
     * @return OAuthIntegration
     */
    public function setOauthThirdParty($oauth_third_party)
    {
        $this->oauth_third_party = $oauth_third_party;

        return $this;
    }
}
