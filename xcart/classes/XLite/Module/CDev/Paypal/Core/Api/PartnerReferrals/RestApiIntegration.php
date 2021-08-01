<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-rest_api_integration
 *
 * @property string integration_method
 * @property string integration_type
 */
class RestApiIntegration extends PayPalModel
{
    /**
     * @return string
     */
    public function getIntegrationMethod()
    {
        return $this->integration_method;
    }

    /**
     * Valid Values: ["BRAINTREE", "PAYPAL"]
     *
     * @param string $integration_method
     *
     * @return RestApiIntegration
     */
    public function setIntegrationMethod($integration_method)
    {
        $this->integration_method = $integration_method;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntegrationType()
    {
        return $this->integration_type;
    }

    /**
     * Valid Values: ["THIRD_PARTY"]
     *
     * @param string $integration_type
     *
     * @return RestApiIntegration
     */
    public function setIntegrationType($integration_type)
    {
        $this->integration_type = $integration_type;

        return $this;
    }
}
