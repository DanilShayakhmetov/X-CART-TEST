<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-product
 *
 * @property string  name
 * @property string  vetting_status
 * @property bool    active
 */
class Product extends PayPalModel
{
    /**
     * Valid Values: ["EXPRESS_CHECKOUT", "WEBSITE_PAYMENTS_STANDARD", "MASS_PAYMENT", "EMAIL_PAYMENTS",
     * "EBAY_CHECKOUT", "PAYFLOW_LINK", "PAYFLOW_PRO", "WEBSITE_PAYMENTS_PRO_3_0", "WEBSITE_PAYMENTS_PRO_2_0",
     * "VIRTUAL_TERMINAL", "HOSTED_SOLE_SOLUTION", "BILL_ME_LATER", "MOBILE_EXPRESS_CHECKOUT", "PAYPAL_HERE",
     * "MOBILE_IN_STORE", "PAYPAL_STANDARD", "MOBILE_PAYPAL_STANDARD", "MOBILE_PAYMENT_ACCEPTANCE", "PAYPAL_ADVANCED",
     * "PAYPAL_PRO", "ENHANCED_RECURRING_PAYMENTS"]
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Valid Values: ["APPROVED", "PENDING", "DECLINED"]
     *
     * @return string
     */
    public function getVettingStatus()
    {
        return $this->vetting_status;
    }

    /**
     * @param string $vetting_status
     */
    public function setVettingStatus($vetting_status)
    {
        $this->vetting_status = $vetting_status;
    }

    /**
     * @return bool $active
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }
}
