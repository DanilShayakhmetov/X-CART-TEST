<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\PartnerReferrals;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/partner-referrals/#definition-account_identifier
 *
 * @property string type
 * @property string value
 */
class AccountIdentifier extends PayPalModel
{
    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Valid Values: ["PAYER_ID"]
     *
     * @param string $type
     *
     * @return AccountIdentifier
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
     * @return AccountIdentifier
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
