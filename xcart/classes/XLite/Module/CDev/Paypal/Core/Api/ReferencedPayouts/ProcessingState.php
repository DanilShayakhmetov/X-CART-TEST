<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\ReferencedPayouts;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/referenced-payouts/#definition-processing_state
 *
 * @property string status
 * @property string reason
 */
class ProcessingState extends PayPalModel
{
    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Valid Values: [PENDING, PROCESSING, SUCCESS, FAILED]
     *
     * @param string $status
     *
     * @return ProcessingState
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Valid Values: [INTERNAL_ERROR, NOT_ENOUGH_BALANCE, AMOUNT_CHECK_FAILED, MERCHANT_PARTNER_PERMISSIONS_ISSUE,
     *                MERCHANT_RESTRICTIONS, TRANSACTION_UNDER_DISPUTE, TRANSACTION_NOT_VALID, UNSUPPORTED_CURRENCY]
     *
     * @param string $reason
     *
     * @return ProcessingState
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }
}
