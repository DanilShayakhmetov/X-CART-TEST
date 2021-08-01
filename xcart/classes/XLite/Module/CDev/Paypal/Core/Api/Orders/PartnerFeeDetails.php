<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\Orders;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/orders/#definition-partner_fee_details
 *
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\Payee    receiver
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\Currency amount
 */
class PartnerFeeDetails extends PayPalModel
{
    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\Payee
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\Payee $receiver
     *
     * @return PartnerFeeDetails
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\Currency
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\Currency $amount
     *
     * @return PartnerFeeDetails
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }
}
