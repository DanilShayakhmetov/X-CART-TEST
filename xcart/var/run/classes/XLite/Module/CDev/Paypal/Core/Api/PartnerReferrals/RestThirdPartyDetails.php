<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-rest_third_party_details
 *
 * @property string   partner_client_id
 * @property string[] feature_list
 */
class RestThirdPartyDetails extends PayPalModel
{
    /**
     * @return string
     */
    public function getPartnerClientId()
    {
        return $this->partner_client_id;
    }

    /**
     * @param string $partner_client_id
     *
     * @return RestThirdPartyDetails
     */
    public function setPartnerClientId($partner_client_id)
    {
        $this->partner_client_id = $partner_client_id;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getFeatureList()
    {
        return $this->feature_list;
    }

    /**
     * Valid Values: ["PAYMENT", "REFUND", "FUTURE_PAYMENT", "DIRECT_PAYMENT", "PARTNER_FEE"]
     *
     * @param string[] $feature_list
     *
     * @return RestThirdPartyDetails
     */
    public function setFeatureList($feature_list)
    {
        $this->feature_list = $feature_list;

        return $this;
    }

    /**
     * @param string $feature
     *
     * @return RestThirdPartyDetails
     */
    public function addFeature($feature)
    {
        if (!$this->getFeatureList()) {

            return $this->setFeatureList([$feature]);
        }

        return $this->setFeatureList(
            array_merge($this->getFeatureList(), [$feature])
        );
    }

    /**
     * @param string $feature
     *
     * @return RestThirdPartyDetails
     */
    public function removeFeature($feature)
    {
        return $this->setFeatureList(
            array_diff($this->getFeatureList(), [$feature])
        );
    }
}
