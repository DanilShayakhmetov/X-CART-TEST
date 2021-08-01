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
 * https://developer.paypal.com/docs/api/payments/#definition-refund_request
 *
 * @property \PayPal\Api\Amount amount
 * @property string             description
 * @property string             reason
 * @property string             invoice_number
 * @property string             custom
 */
class RefundRequest extends PayPalResourceModel
{
    /**
     * @return \PayPal\Api\Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param \PayPal\Api\Amount $amount
     *
     * @return RefundRequest
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

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
     * @return RefundRequest
     */
    public function setDescription($description)
    {
        $this->description = $description;

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
     * @param string $reason
     *
     * @return RefundRequest
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

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
     * @return RefundRequest
     */
    public function setInvoiceNumber($invoice_number)
    {
        $this->invoice_number = $invoice_number;

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
     * @return RefundRequest
     */
    public function setCustom($custom)
    {
        $this->custom = $custom;

        return $this;
    }

    /**
     * @param string         $captureId
     * @param string         $payerId
     * @param ApiContext     $apiContext is the APIContext for this call. It can be used to pass dynamic configuration
     *                                   and credentials.
     * @param PayPalRestCall $restCall   is the Rest Call Service that is used to make rest calls
     *
     * @return Refund
     */
    public function refundCapture($captureId, $payerId, $apiContext = null, $restCall = null)
    {
        ArgumentValidator::validate($this->getAmount(), 'Amount');
        ArgumentValidator::validate($this->getInvoiceNumber(), 'InvoiceNumber');
        ArgumentValidator::validate($this->getCustom(), 'Custom');

        $payLoad = $this->toJSON();

        $clientId      = $apiContext->getCredential()->getClientId();
        $authAssertion = base64_encode('{"alg": "none"}') . '.'
            . base64_encode(sprintf('{"iss": "%s", "payer_id": "%s"}', $clientId, $payerId)) . '.';

        $json = self::executeCall(
            '/v1/payments/capture/' . $captureId . '/refund',
            'POST',
            $payLoad,
            ['PayPal-Auth-Assertion' => $authAssertion],
            $apiContext,
            $restCall
        );

        return (new Refund())->fromJson($json);
    }
}
