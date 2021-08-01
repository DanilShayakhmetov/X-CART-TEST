<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\Api\Orders;

use PayPal\Common\PayPalResourceModel;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use PayPal\Transport\PayPalRestCall;
use PayPal\Validation\ArgumentValidator;

/**
 * https://developer.paypal.com/docs/api/orders/#definition-order
 *
 * @property string                                                       id
 * @property string                                                       intent
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\PurchaseUnit[]     purchase_units
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\PaymentDetails     payment_details
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\ApplicationContext application_context
 * @property \PayPal\Api\PayerInfo                                        payer_info
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\Metadata           metadata
 * @property string                                                       status
 * @property \XLite\Module\CDev\Paypal\Core\Api\Orders\RedirectUrls       redirect_urls
 * @property string                                                       create_time
 * @property string                                                       update_time
 */
class Order extends PayPalResourceModel
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
     * @return Order
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * Valid Values: []
     *
     * @param string $intent
     *
     * @return Order
     */
    public function setIntent($intent)
    {
        $this->intent = $intent;

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
     * @return Order
     */
    public function setPurchaseUnits($purchase_units)
    {
        $this->purchase_units = $purchase_units;

        return $this;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\PurchaseUnit $purchase_unit
     *
     * @return Order
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
     * @return Order
     */
    public function removePurchaseUnit($purchase_unit)
    {
        return $this->setPurchaseUnits(
            array_diff($this->getPurchaseUnits(), [$purchase_unit])
        );
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\PaymentDetails
     */
    public function getPaymentDetails()
    {
        return $this->payment_details;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\PaymentDetails $payment_details
     *
     * @return Order
     */
    public function setPaymentDetails($payment_details)
    {
        $this->payment_details = $payment_details;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\ApplicationContext
     */
    public function getApplicationContext()
    {
        return $this->application_context;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\ApplicationContext $application_context
     *
     * @return Order
     */
    public function setApplicationContext($application_context)
    {
        $this->application_context = $application_context;

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
     * @return Order
     */
    public function setPayerInfo($payer_info)
    {
        $this->payer_info = $payer_info;

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
     * @return Order
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;

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
     * @return Order
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return \XLite\Module\CDev\Paypal\Core\Api\Orders\RedirectUrls
     */
    public function getRedirectUrls()
    {
        return $this->redirect_urls;
    }

    /**
     * @param \XLite\Module\CDev\Paypal\Core\Api\Orders\RedirectUrls $redirect_urls
     *
     * @return Order
     */
    public function setRedirectUrls($redirect_urls)
    {
        $this->redirect_urls = $redirect_urls;

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
     * @return Order
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
     * @return Order
     */
    public function setUpdateTime($update_time)
    {
        $this->update_time = $update_time;

        return $this;
    }

    /**
     * @param ApiContext     $apiContext is the APIContext for this call. It can be used to pass dynamic configuration
     *                                   and credentials.
     * @param PayPalRestCall $restCall   is the Rest Call Service that is used to make rest calls
     *
     * @return Order
     */
    public function create($apiContext = null, $restCall = null)
    {
        ArgumentValidator::validate($this->getPurchaseUnits(), 'PurchaseUnits');
        ArgumentValidator::validate($this->getRedirectUrls(), 'RedirectUrls');

        $payLoad = $this->toJSON();

        $json = self::executeCall(
            '/v1/checkout/orders',
            'POST',
            $payLoad,
            null,
            $apiContext,
            $restCall
        );

        return $this->fromJson($json);
    }

    /**
     * @param ApiContext     $apiContext is the APIContext for this call. It can be used to pass dynamic configuration
     *                                   and credentials.
     * @param PayPalRestCall $restCall   is the Rest Call Service that is used to make rest calls
     *
     * @return Order
     */
    public function update($apiContext = null, $restCall = null)
    {
        ArgumentValidator::validate($this->getPurchaseUnits(), 'PurchaseUnits');

        $payLoad = $this->toJSON();

        $json = self::executeCall(
            '/v1/checkout/orders/' . $this->getId(),
            'PATCH',
            $payLoad,
            null,
            $apiContext,
            $restCall
        );

        return $this->fromJson($json);
    }

    /**
     * @param string         $orderId
     * @param ApiContext     $apiContext is the APIContext for this call. It can be used to pass dynamic configuration
     *                                   and credentials.
     * @param PayPalRestCall $restCall   is the Rest Call Service that is used to make rest calls
     *
     * @return boolean
     */
    public static function cancel($orderId, $apiContext = null, $restCall = null)
    {
        $payLoad = '';

        self::executeCall(
            '/v1/checkout/orders/' . $orderId,
            'DELETE',
            $payLoad,
            null,
            $apiContext,
            $restCall
        );

        return true;
    }

    /**
     * @param string         $orderId
     * @param ApiContext     $apiContext is the APIContext for this call. It can be used to pass dynamic configuration
     *                                   and credentials.
     * @param PayPalRestCall $restCall   is the Rest Call Service that is used to make rest calls
     *
     * @return Order
     * @throws PayPalConnectionException
     */
    public static function get($orderId, $apiContext = null, $restCall = null)
    {
        $payLoad = '';

        $json = self::executeCall(
            '/v1/checkout/orders/' . $orderId,
            'GET',
            $payLoad,
            null,
            $apiContext,
            $restCall
        );

        return (new self)->fromJson($json);
    }
}
