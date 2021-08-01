<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-email_contact
 *
 * @property string email_address
 * @property string role
 */
class EmailContact extends PayPalModel
{
    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->email_address;
    }

    /**
     * @param string $email_address
     *
     * @return EmailContact
     */
    public function setEmailAddress($email_address)
    {
        $this->email_address = $email_address;

        return $this;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Valid Values: ["CUSTOMER_SERVICE"]
     *
     * @param string $role
     *
     * @return EmailContact
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }
}
