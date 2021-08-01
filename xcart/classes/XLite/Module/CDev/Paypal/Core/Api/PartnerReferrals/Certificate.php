<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-certificate
 *
 * @property string api_user_name
 * @property string api_password
 * @property string fingerprint
 * @property string download_link
 */
class Certificate extends PayPalModel
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
     * @return Certificate
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
     * @return Certificate
     */
    public function setApiPassword($api_password)
    {
        $this->api_password = $api_password;

        return $this;
    }

    /**
     * @return string
     */
    public function getFingerprint()
    {
        return $this->fingerprint;
    }

    /**
     * @param string $fingerprint
     *
     * @return Certificate
     */
    public function setFingerprint($fingerprint)
    {
        $this->fingerprint = $fingerprint;

        return $this;
    }

    /**
     * @return string
     */
    public function getDownloadLink()
    {
        return $this->download_link;
    }

    /**
     * @param string $download_link
     *
     * @return Certificate
     */
    public function setDownloadLink($download_link)
    {
        $this->download_link = $download_link;

        return $this;
    }
}
