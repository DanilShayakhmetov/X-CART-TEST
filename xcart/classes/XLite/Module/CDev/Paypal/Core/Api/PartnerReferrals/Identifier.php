<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-identifier
 *
 * @property string type
 * @property string value
 */
class Identifier extends PayPalModel
{
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Valid Values: ["ROUTING_NUMBER_1", "ROUTING_NUMBER_2", "ROUTING_NUMBER_3", "BI_CODE", "BANK_CODE",
     * "BRANCH_CODE", "INTERMEDIARY_SWIFT_CODE", "BBAN", "BBAN_ENCRYPTED", "BBAN_HMAC", "AGGREGATOR_YODLEE"]
     *
     * @param string $type
     *
     * @return Identifier
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return Identifier
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
