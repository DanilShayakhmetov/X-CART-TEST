<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-signature
 *
 * @property string api_user_name
 * @property string api_password
 * @property string signature
 */
class Signature extends PayPalModel
{
    /**
     * @return string
     */
    public function getApiUserName()
    {
        return $this->api_user_name;
    }

    /**
     * @param string $api_user_name
     *
     * @return Signature
     */
    public function setApiUserName($api_user_name)
    {
        $this->api_user_name = $api_user_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiPassword()
    {
        return $this->api_password;
    }

    /**
     * @param string $api_password
     *
     * @return Signature
     */
    public function setApiPassword($api_password)
    {
        $this->api_password = $api_password;

        return $this;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param string $signature
     *
     * @return Signature
     */
    public function setSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }
}
