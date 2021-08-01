<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\Orders;

use PayPal\Common\PayPalResourceModel;

/**
 * https://developer.paypal.com/docs/api/orders/#definition-refund
 *
 * @property string                                           id
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\Amount amount
 * @property string                                           capture_id
 * @property string                                           sale_id
 * @property string                                           status
 */
class Refund extends PayPalResourceModel
{
    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return Refund
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\Amount $amount
     *
     * @return Refund
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getCaptureId()
    {
        return $this->capture_id;
    }

    /**
     * @param string $capture_id
     *
     * @return Refund
     */
    public function setCaptureId($capture_id)
    {
        $this->capture_id = $capture_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getSaleId()
    {
        return $this->sale_id;
    }

    /**
     * @param string $sale_id
     *
     * @return Refund
     */
    public function setSaleId($sale_id)
    {
        $this->sale_id = $sale_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Valid Values: ["PENDING", "COMPLETED", "FAILED"]
     *
     * @param string $status
     *
     * @return Refund
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
}
