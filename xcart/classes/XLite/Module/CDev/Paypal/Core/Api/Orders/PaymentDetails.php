<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\Orders;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/orders/#definition-payment_details
 *
 * @property string payment_id
 * @property string disbursement_mode
 */
class PaymentDetails extends PayPalModel
{
    /**
     * @return string
     */
    public function getPaymentId()
    {
        return $this->payment_id;
    }

    /**
     * @param string $payment_id
     *
     * @return PaymentDetails
     */
    public function setPaymentId($payment_id)
    {
        $this->payment_id = $payment_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisbursementMode()
    {
        return $this->disbursement_mode;
    }

    /**
     * Valid Values: ["INSTANT", "DELAYED"]
     *
     * @param string $disbursement_mode
     *
     * @return PaymentDetails
     */
    public function setDisbursementMode($disbursement_mode)
    {
        $this->disbursement_mode = $disbursement_mode;

        return $this;
    }
}
