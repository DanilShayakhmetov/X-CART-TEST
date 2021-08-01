<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\Orders;

use PayPal\Common\PayPalResourceModel;
use PayPal\Rest\ApiContext;
use PayPal\Transport\PayPalRestCall;
use PayPal\Validation\ArgumentValidator;
use XLite\Module\CDev\Paypal\Core\Api\Payments\RefundRequest;

/**
 * https://developer.paypal.com/docs/api/orders/#definition-capture
 *
 * @property string                                             id
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\Amount   amount
 * @property string                                             status
 * @property string                                             reason_code
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\Currency transaction_fee
 */
class Capture extends PayPalResourceModel
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
     * @return Capture
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
     * @return Capture
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

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
     * Valid Values: ["PENDING", "COMPLETED", "REFUNDED", "PARTIALLY_REFUNDED", "DENIED"]
     *
     * @param string $status
     *
     * @return Capture
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
     * Valid Values: ["CHARGEBACK", "GUARANTEE", "BUYER_COMPLAINT", "REFUND", "UNCONFIRMED_SHIPPING_ADDRESS", "ECHECK",
     * "INTERNATIONAL_WITHDRAWAL", "RECEIVING_PREFERENCE_MANDATES_MANUAL_ACTION", "PAYMENT_REVIEW",
     * "REGULATORY_REVIEW", "UNILATERAL", "VERIFICATION_REQUIRED", "DELAYED_DISBURSEMENT"]
     *
     * @param string $reason_code
     *
     * @return Capture
     */
    public function setReasonCode($reason_code)
    {
        $this->reason_code = $reason_code;

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
     * @return Capture
     */
    public function setTransactionFee($transaction_fee)
    {
        $this->transaction_fee = $transaction_fee;

        return $this;
    }

    /**
     * @param string         $invoiceNumber
     * @param string         $custom
     * @param string         $payerEmail
     * @param ApiContext     $apiContext is the APIContext for this call. It can be used to pass dynamic configuration
     *                                   and credentials.
     * @param PayPalRestCall $restCall   is the Rest Call Service that is used to make rest calls
     *
     * @return \XLite\Module\CDev\Paypal\Core\Api\Payments\Refund
     */
    public function refund($invoiceNumber, $custom, $payerEmail, $apiContext = null, $restCall = null)
    {
        $refundRequest = new RefundRequest();
        $refundRequest->setAmount($this->getAmount());
        $refundRequest->setInvoiceNumber($invoiceNumber);
        $refundRequest->setCustom($custom);

        ArgumentValidator::validate($refundRequest->getAmount(), 'Amount');
        ArgumentValidator::validate($refundRequest->getInvoiceNumber(), 'InvoiceNumber');
        ArgumentValidator::validate($refundRequest->getCustom(), 'Custom');

        $payLoad = $refundRequest->toJSON();

        $clientId      = $apiContext->getCredential()->getClientId();
        $authAssertion = base64_encode('{"alg": "none"}') . '.'
            . base64_encode(sprintf('{"iss": "%s", "email": "%s"}', $clientId, $payerEmail)) . '.';

        $json = self::executeCall(
            '/v1/payments/capture/' . $this->getId() . '/refund',
            'POST',
            $payLoad,
            ['PayPal-Auth-Assertion' => $authAssertion],
            $apiContext,
            $restCall
        );

        return (new \XLite\Module\CDev\Paypal\Core\Api\Payments\Refund())->fromJson($json);
    }
}
