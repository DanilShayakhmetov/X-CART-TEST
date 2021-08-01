<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\Orders;

use PayPal\Common\PayPalResourceModel;

/**
 * https://developer.paypal.com/docs/api/orders/#definition-sale
 *
 * @property string                                             id
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\Amount   amount
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\Currency transaction_fee
 * @property string                                             status
 * @property string                                             create_time
 * @property string                                             update_time
 */
class Sale extends PayPalResourceModel
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
     * @return Sale
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
     * @return Sale
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\Currency
     */
    public function getTransactionFee()
    {
        return $this->transaction_fee;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\Currency $transaction_fee
     *
     * @return Sale
     */
    public function setTransactionFee($transaction_fee)
    {
        $this->transaction_fee = $transaction_fee;

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
     * Valid Values: ["COMPLETED", "PARTIALLY_REFUNDED", "PENDING", "REFUNDED", "DENIED"]
     *
     * @param string $status
     *
     * @return Sale
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
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
     *
     * @return Sale
     */
    public function setCreateTime($create_time)
    {
        $this->create_time = $create_time;

        return $this;
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
     *
     * @return Sale
     */
    public function setUpdateTime($update_time)
    {
        $this->update_time = $update_time;

        return $this;
    }
}
