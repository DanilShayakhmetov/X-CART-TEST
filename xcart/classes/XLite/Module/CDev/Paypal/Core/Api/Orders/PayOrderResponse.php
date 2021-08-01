<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\Orders;

use PayPal\Common\PayPalResourceModel;

/**
 * https://developer.paypal.com/docs/api/orders/#definition-pay_order_response
 *
 * @property string                                                   order_id
 * @property string                                                   status
 * @property string                                                   intent
 * @property \PayPal\Api\PayerInfo                                    payer_info
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\PurchaseUnit[] purchase_units
 * @property string                                                   create_time
 * @property string                                                   update_time
 */
class PayOrderResponse extends PayPalResourceModel
{
    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * @param string $order_id
     *
     * @return PayOrderResponse
     */
    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;

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
     * @param string $status
     *
     * @return PayOrderResponse
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getIntent()
    {
        return $this->intent;
    }

    /**
     * @param string $intent
     *
     * @return PayOrderResponse
     */
    public function setIntent($intent)
    {
        $this->intent = $intent;

        return $this;
    }

    /**
     * @return \PayPal\Api\PayerInfo
     */
    public function getPayerInfo()
    {
        return $this->payer_info;
    }

    /**
     * @param \PayPal\Api\PayerInfo $payer_info
     *
     * @return PayOrderResponse
     */
    public function setPayerInfo($payer_info)
    {
        $this->payer_info = $payer_info;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\PurchaseUnit[]
     */
    public function getPurchaseUnits()
    {
        return $this->purchase_units;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\PurchaseUnit[] $purchase_units
     *
     * @return PayOrderResponse
     */
    public function setPurchaseUnits($purchase_units)
    {
        $this->purchase_units = $purchase_units;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\PurchaseUnit $purchase_unit
     *
     * @return PayOrderResponse
     */
    public function addPurchaseUnit($purchase_unit)
    {
        if (!$this->getPurchaseUnits()) {

            return $this->setPurchaseUnits([$purchase_unit]);
        }

        return $this->setPurchaseUnits(
            array_merge($this->getPurchaseUnits(), [$purchase_unit])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\PurchaseUnit $purchase_unit
     *
     * @return PayOrderResponse
     */
    public function removePurchaseUnit($purchase_unit)
    {
        return $this->setPurchaseUnits(
            array_diff($this->getPurchaseUnits(), [$purchase_unit])
        );
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
     * @return PayOrderResponse
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
     * @return PayOrderResponse
     */
    public function setUpdateTime($update_time)
    {
        $this->update_time = $update_time;

        return $this;
    }
}
