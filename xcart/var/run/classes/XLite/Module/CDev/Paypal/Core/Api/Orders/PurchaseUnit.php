<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\Orders;

use PayPal\Common\PayPalModel;

/**
 * https://developer.paypal.com/docs/api/orders/#definition-purchase_unit
 *
 * @property string                                                      reference_id
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\Amount            amount
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\Payee             payee
 * @property string                                                      description
 * @property string                                                      custom
 * @property string                                                      invoice_number
 * @property string                                                      payment_descriptor
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\Item[]            items
 * @property string                                                      notify_url
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\ShippingAddress   shipping_address
 * @property string                                                      shipping_method
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\PartnerFeeDetails partner_fee_details
 * @property integer                                                     payment_linked_group
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\Metadata          metadata
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\PaymentSummary    payment_summary
 * @property string                                                      status
 * @property string                                                      reason_code
 */
class PurchaseUnit extends PayPalModel
{
    /**
     * @return string
     */
    public function getReferenceId()
    {
        return $this->reference_id;
    }

    /**
     * @param string $reference_id
     *
     * @return PurchaseUnit
     */
    public function setReferenceId($reference_id)
    {
        $this->reference_id = $reference_id;

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
     * @return PurchaseUnit
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\Payee
     */
    public function getPayee()
    {
        return $this->payee;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\Payee $payee
     *
     * @return PurchaseUnit
     */
    public function setPayee($payee)
    {
        $this->payee = $payee;

        return $this;
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
     *
     * @return PurchaseUnit
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustom()
    {
        return $this->custom;
    }

    /**
     * @param string $custom
     *
     * @return PurchaseUnit
     */
    public function setCustom($custom)
    {
        $this->custom = $custom;

        return $this;
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
     *
     * @return PurchaseUnit
     */
    public function setInvoiceNumber($invoice_number)
    {
        $this->invoice_number = $invoice_number;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentDescriptor()
    {
        return $this->payment_descriptor;
    }

    /**
     * @param string $payment_descriptor
     *
     * @return PurchaseUnit
     */
    public function setPaymentDescriptor($payment_descriptor)
    {
        $this->payment_descriptor = $payment_descriptor;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\Item[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\Item[] $items
     *
     * @return PurchaseUnit
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\Item $items
     *
     * @return PurchaseUnit
     */
    public function addItem($items)
    {
        if (!$this->getItems()) {

            return $this->setItems([$items]);
        }

        return $this->setItems(
            array_merge($this->getItems(), [$items])
        );
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\Item $items
     *
     * @return PurchaseUnit
     */
    public function removeItem($items)
    {
        return $this->setItems(
            array_diff($this->getItems(), [$items])
        );
    }

    /**
     * @return string
     */
    public function getNotifyUrl()
    {
        return $this->notify_url;
    }

    /**
     * @param string $notify_url
     *
     * @return PurchaseUnit
     */
    public function setNotifyUrl($notify_url)
    {
        $this->notify_url = $notify_url;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\ShippingAddress
     */
    public function getShippingAddress()
    {
        return $this->shipping_address;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\ShippingAddress $shipping_address
     *
     * @return PurchaseUnit
     */
    public function setShippingAddress($shipping_address)
    {
        $this->shipping_address = $shipping_address;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->shipping_method;
    }

    /**
     * @param string $shipping_method
     *
     * @return PurchaseUnit
     */
    public function setShippingMethod($shipping_method)
    {
        $this->shipping_method = $shipping_method;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\PartnerFeeDetails
     */
    public function getPartnerFeeDetails()
    {
        return $this->partner_fee_details;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\PartnerFeeDetails $partner_fee_details
     *
     * @return PurchaseUnit
     */
    public function setPartnerFeeDetails($partner_fee_details)
    {
        $this->partner_fee_details = $partner_fee_details;

        return $this;
    }

    /**
     * @return int
     */
    public function getPaymentLinkedGroup()
    {
        return $this->payment_linked_group;
    }

    /**
     * @param int $payment_linked_group
     *
     * @return PurchaseUnit
     */
    public function setPaymentLinkedGroup($payment_linked_group)
    {
        $this->payment_linked_group = $payment_linked_group;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\Metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\Metadata $metadata
     *
     * @return PurchaseUnit
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\PaymentSummary
     */
    public function getPaymentSummary()
    {
        return $this->payment_summary;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\PaymentSummary $payment_summary
     *
     * @return PurchaseUnit
     */
    public function setPaymentSummary($payment_summary)
    {
        $this->payment_summary = $payment_summary;

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
     * Valid Values: ["NOT_PROCESSED", "PENDING", "VOIDED", "AUTHORIZED", "CAPTURED"]
     *
     * @param string $status
     *
     * @return PurchaseUnit
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getReasonCode()
    {
        return $this->reason_code;
    }

    /**
     * Valid Values: ["PAYER_SHIPPING_UNCONFIRMED", "MULTI_CURRENCY", "RISK_REVIEW", "REGULATORY_REVIEW",
     * "VERIFICATION_REQUIRED", "ORDER", "OTHER", "DECLINED_BY_POLICY"]
     *
     * @param string $reason_code
     *
     * @return PurchaseUnit
     */
    public function setReasonCode($reason_code)
    {
        $this->reason_code = $reason_code;

        return $this;
    }
}
