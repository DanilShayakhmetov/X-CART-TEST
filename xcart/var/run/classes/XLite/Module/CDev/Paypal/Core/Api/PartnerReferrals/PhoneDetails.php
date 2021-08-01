<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-phone_details
 *
 * @property \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Phone phone_number_details
 * @property string                                                    phone_type
 */
class PhoneDetails extends PayPalModel
{
    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Phone
     */
    public function getPhoneNumberDetails()
    {
        return $this->phone_number_details;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals\Phone $phone_number_details
     *
     * @return PhoneDetails
     */
    public function setPhoneNumberDetails($phone_number_details)
    {
        $this->phone_number_details = $phone_number_details;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneType()
    {
        return $this->phone_type;
    }

    /**
     * Valid Values: ["FAX", "HOME", "MOBILE", "OTHER", "PAGER"]
     *
     * @param string $phone_type
     *
     * @return PhoneDetails
     */
    public function setPhoneType($phone_type)
    {
        $this->phone_type = $phone_type;

        return $this;
    }
}
