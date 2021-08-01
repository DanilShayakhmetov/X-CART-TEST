<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-phone
 *
 * @property string country_code
 * @property string national_number
 * @property string extension_number
 */
class Phone extends PayPalModel
{
    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->country_code;
    }

    /**
     * @param string $country_code
     *
     * @return Phone
     */
    public function setCountryCode($country_code)
    {
        $this->country_code = $country_code;

        return $this;
    }

    /**
     * @return string
     */
    public function getNationalNumber()
    {
        return $this->national_number;
    }

    /**
     * @param string $national_number
     *
     * @return Phone
     */
    public function setNationalNumber($national_number)
    {
        $this->national_number = $national_number;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtensionNumber()
    {
        return $this->extension_number;
    }

    /**
     * @param string $extension_number
     *
     * @return Phone
     */
    public function setExtensionNumber($extension_number)
    {
        $this->extension_number = $extension_number;

        return $this;
    }
}
