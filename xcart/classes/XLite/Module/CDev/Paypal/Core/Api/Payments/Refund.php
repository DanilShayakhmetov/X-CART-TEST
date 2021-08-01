<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\Payments;

use PayPal\Common\PayPalResourceModel;
use PayPal\Rest\ApiContext;
use PayPal\Transport\PayPalRestCall;
use PayPal\Validation\ArgumentValidator;

/**
 * https://developer.paypal.com/docs/api/payments/#definition-refund
 *
 * @property string             id
 * @property \PayPal\Api\Amount amount
 * @property string             state
 * @property string             reason
 * @property string             invoice_number
 * @property string             sale_id
 * @property string             capture_id
 * @property string             parent_payment
 * @property string             description
 * @property string             create_time
 * @property string             update_time
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
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return \PayPal\Api\Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param \PayPal\Api\Amount $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->invoice_number;
    }

    /**
     * @param string $invoice_number
     */
    public function setInvoiceNumber($invoice_number)
    {
        $this->invoice_number = $invoice_number;
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
     */
    public function setSaleId($sale_id)
    {
        $this->sale_id = $sale_id;
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
     */
    public function setCaptureId($capture_id)
    {
        $this->capture_id = $capture_id;
    }

    /**
     * @return string
     */
    public function getParentPayment()
    {
        return $this->parent_payment;
    }

    /**
     * @param string $parent_payment
     */
    public function setParentPayment($parent_payment)
    {
        $this->parent_payment = $parent_payment;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCreateTime()
    {
        return $this->create_time;
    }

    /**
     * @param string $create_time
     */
    public function setCreateTime($create_time)
    {
        $this->create_time = $create_time;
    }

    /**
     * @return string
     */
    public function getUpdateTime()
    {
        return $this->update_time;
    }

    /**
     * @param string $update_time
     */
    public function setUpdateTime($update_time)
    {
        $this->update_time = $update_time;
    }
}
