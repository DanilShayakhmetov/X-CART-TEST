<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\ReferencedPayouts;

use PayPal\Common\PayPalResourceModel;
use PayPal\Rest\ApiContext;
use PayPal\Transport\PayPalRestCall;
use PayPal\Validation\ArgumentValidator;

/**
 * https://developer.paypal.com/docs/api/referenced-payouts/#definition-referenced_payouts_item
 *
 * @property string                                                               item_id
 * @property \XLite\Module\CDev\Paypal\Core\Api\ReferencedPayouts\ProcessingState processing_state
 * @property string                                                               reference_id
 * @property string                                                               reference_type
 * @property string                                                               payout_transaction_id
 * @property string                                                               external_merchant_id
 * @property string                                                               external_reference_id
 * @property string                                                               payee_email
 * @property \XLite\Module\CDev\Paypal\Core\Api\ReferencedPayouts\Money           payout_amount
 * @property string                                                               payout_destination
 * @property string                                                               invoice_id
 * @property string                                                               custom
 */
class ReferencedPayoutsItem extends PayPalResourceModel
{
    /**
     * @return string
     */
    public function getItemId()
    {
        return $this->item_id;
    }

    /**
     * @param string $item_id
     *
     * @return ReferencedPayoutsItem
     */
    public function setItemId($item_id)
    {
        $this->item_id = $item_id;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\ReferencedPayouts\ProcessingState
     */
    public function getProcessingState()
    {
        return $this->processing_state;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\ReferencedPayouts\ProcessingState $processing_state
     *
     * @return ReferencedPayoutsItem
     */
    public function setProcessingState($processing_state)
    {
        $this->processing_state = $processing_state;

        return $this;
    }

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
     * @return ReferencedPayoutsItem
     */
    public function setReferenceId($reference_id)
    {
        $this->reference_id = $reference_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceType()
    {
        return $this->reference_type;
    }

    /**
     * @param string $reference_type
     *
     * @return ReferencedPayoutsItem
     */
    public function setReferenceType($reference_type)
    {
        $this->reference_type = $reference_type;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayoutTransactionId()
    {
        return $this->payout_transaction_id;
    }

    /**
     * @param string $payout_transaction_id
     *
     * @return ReferencedPayoutsItem
     */
    public function setPayoutTransactionId($payout_transaction_id)
    {
        $this->payout_transaction_id = $payout_transaction_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getExternalMerchantId()
    {
        return $this->external_merchant_id;
    }

    /**
     * @param string $external_merchant_id
     *
     * @return ReferencedPayoutsItem
     */
    public function setExternalMerchantId($external_merchant_id)
    {
        $this->external_merchant_id = $external_merchant_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getExternalReferenceId()
    {
        return $this->external_reference_id;
    }

    /**
     * @param string $external_reference_id
     *
     * @return ReferencedPayoutsItem
     */
    public function setExternalReferenceId($external_reference_id)
    {
        $this->external_reference_id = $external_reference_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayeeEmail()
    {
        return $this->payee_email;
    }

    /**
     * @param string $payee_email
     *
     * @return ReferencedPayoutsItem
     */
    public function setPayeeEmail($payee_email)
    {
        $this->payee_email = $payee_email;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\ReferencedPayouts\Money
     */
    public function getPayoutAmount()
    {
        return $this->payout_amount;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\ReferencedPayouts\Money $payout_amount
     *
     * @return ReferencedPayoutsItem
     */
    public function setPayoutAmount($payout_amount)
    {
        $this->payout_amount = $payout_amount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPayoutDestination()
    {
        return $this->payout_destination;
    }

    /**
     * @param string $payout_destination
     *
     * @return ReferencedPayoutsItem
     */
    public function setPayoutDestination($payout_destination)
    {
        $this->payout_destination = $payout_destination;

        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceId()
    {
        return $this->invoice_id;
    }

    /**
     * @param string $invoice_id
     *
     * @return ReferencedPayoutsItem
     */
    public function setInvoiceId($invoice_id)
    {
        $this->invoice_id = $invoice_id;

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
     * @return ReferencedPayoutsItem
     */
    public function setCustom($custom)
    {
        $this->custom = $custom;

        return $this;
    }

    /**
     * @param ApiContext     $apiContext is the APIContext for this call. It can be used to pass dynamic configuration
     *                                   and credentials.
     * @param PayPalRestCall $restCall   is the Rest Call Service that is used to make rest calls
     *
     * @return ReferencedPayoutsItem
     */
    public function create($apiContext = null, $restCall = null)
    {
        ArgumentValidator::validate($this->getReferenceId(), 'ReferenceId');
        ArgumentValidator::validate($this->getReferenceType(), 'ReferenceType');

        $payLoad = $this->toJSON();

        $json = self::executeCall(
            '/v1/payments/referenced-payouts-items',
            'POST',
            $payLoad,
            null,
            $apiContext,
            $restCall
        );

        return $this->fromJson($json);
    }
}
