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

/**
 * https://developer.paypal.com/docs/api/orders/#definition-pay_order_request
 *
 * @property string            disbursement_mode
 * @property \PayPal\Api\Payer payer
 */
class PayOrderRequest extends PayPalResourceModel
{
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
     * @return PayOrderRequest
     */
    public function setDisbursementMode($disbursement_mode)
    {
        $this->disbursement_mode = $disbursement_mode;

        return $this;
    }

    /**
     * @return \PayPal\Api\Payer
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * @param \PayPal\Api\Payer $payer
     *
     * @return PayOrderRequest
     */
    public function setPayer($payer)
    {
        $this->payer = $payer;

        return $this;
    }

    /**
     * @param string         $orderId
     * @param ApiContext     $apiContext is the APIContext for this call. It can be used to pass dynamic configuration
     *                                   and credentials.
     * @param PayPalRestCall $restCall   is the Rest Call Service that is used to make rest calls
     *
     * @return PayOrderResponse
     */
    public function pay($orderId, $apiContext = null, $restCall = null)
    {
        ArgumentValidator::validate($this->getDisbursementMode(), 'disbursementMode');

        $payLoad = $this->toJSON();

        $json = self::executeCall(
            '/v1/checkout/orders/' . $orderId . '/pay',
            'POST',
            $payLoad,
            null,
            $apiContext,
            $restCall
        );

        return new PayOrderResponse($json);
    }
}
