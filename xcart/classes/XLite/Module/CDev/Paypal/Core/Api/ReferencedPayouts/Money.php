<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\ReferencedPayouts;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/referenced-payouts/#definition-money
 *
 * @property string currency_code
 * @property string value
 */
class Money extends PayPalModel
{
    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currency_code;
    }

    /**
     * @param string $currency_code
     *
     * @return Money
     */
    public function setCurrencyCode($currency_code)
    {
        $this->currency_code = $currency_code;

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
     * @return Money
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
