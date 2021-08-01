<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\PaypalCommercePlatform;

class Webhook
{
    /**
     * @param $data
     *
     * @return bool
     */
    public function isWebhookData($data)
    {
        return isset($data['event_type'], $data['resource_type'])
            && $this->isValidResource($data['event_type'], $data['resource_type']);
    }

    public function detectTransaction($data)
    {
        $resourceType = $data['resource_type'] ?? '';

        if ($resourceType === 'checkout-order') {
            return $this->detectTransactionByCheckoutOrder($data['resource']);
        } elseif ($resourceType === 'capture') {
            return $this->detectTransactionByCapture($data['resource']);
        } elseif ($resourceType === 'authorization') {
            return $this->detectTransactionByAuthorization($data['resource']);
        } elseif ($resourceType === 'refund') {
            return $this->detectTransactionByRefund($data['resource']);
        }
    }

    /**
     * @param string $eventType
     * @param string $resourceType
     *
     * @return bool
     */
    protected function isValidResource($eventType, $resourceType): bool
    {
        return true;
        $resources = [
            //'CHECKOUT.ORDER.APPROVED'   => 'checkout-order',
            //'CHECKOUT.ORDER.COMPLETED'  => 'checkout-order',
            'PAYMENT.CAPTURE.COMPLETED'    => 'capture',
            'PAYMENT.CAPTURE.DENIED'       => 'capture',
            'PAYMENT.AUTHORIZATION.VOIDED' => 'authorization',

            'PAYMENT.CAPTURE.REFUNDED'     => 'refund',
        ];

        return isset($resources[$eventType]) && $resourceType === $resources[$eventType];
    }

    /**
     * @param array $checkoutOrder
     *
     * @return \XLite\Model\Payment\Transaction
     */
    protected function detectTransactionByCheckoutOrder($checkoutOrder)
    {
        $paypalOrderId = $checkoutOrder['id'];

        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')
            ->findOneByCell('PaypalOrderId', $paypalOrderId);
    }

    /**
     * @param array $capture
     *
     * @return \XLite\Model\Payment\Transaction
     */
    protected function detectTransactionByCapture($capture)
    {
        $transactionPublicId = $capture['invoice_id'];

        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')->findOneBy([
            'public_id' => $transactionPublicId,
        ]);
    }

    /**
     * @param array $capture
     *
     * @return \XLite\Model\Payment\Transaction
     */
    protected function detectTransactionByAuthorization($authorization)
    {
        $transactionPublicId = $authorization['invoice_id'];

        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')->findOneBy([
            'public_id' => $transactionPublicId,
        ]);
    }

    /**
     * @param array $capture
     *
     * @return \XLite\Model\Payment\Transaction
     */
    protected function detectTransactionByRefund($refund)
    {
        $transactionPublicId = $refund['invoice_id'];

        return \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')->findOneBy([
            'public_id' => $transactionPublicId,
        ]);
    }
}
