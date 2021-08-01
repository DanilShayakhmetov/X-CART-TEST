<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core\PaypalCommercePlatform\Convert;

use XLite\Logic\Order\Modifier\AModifier;
use XLite\Model\Order;
use XLite\Model\Order\Modifier;
use XLite\Model\Payment\BackendTransaction;
use XLite\Model\Payment\Transaction;

/**
 * @see https://developer.paypal.com/docs/api/orders/v2/#orders-create-request-body
 */
class CreateOrder
{
    /**
     * @param Transaction $transaction
     * @param array       $applicationContext
     *
     * @return array
     */
    public function fromTransaction(Transaction $transaction, array $applicationContext = []): array
    {
        $result = [];

        $result['intent'] = $this->getIntent($transaction);

        if ($payer = $this->getPayer($transaction)) {
            $result['payer'] = $payer;
        }

        if ($purchaseUnits = $this->getPurchaseUnits($transaction)) {
            $result['purchase_units'] = $purchaseUnits;

        } else {
            return [];
        }

        if ($applicationContext) {
            $result['application_context'] = $applicationContext;
        }

        return $result;
    }

    /**
     * @param Transaction $transaction
     *
     * @return string
     */
    protected function getIntent(Transaction $transaction): string
    {
        return $transaction->getType() === BackendTransaction::TRAN_TYPE_AUTH
            ? 'AUTHORIZE'
            : 'CAPTURE';
    }

    /**
     * @param Transaction $transaction
     *
     * @return array
     */
    protected function getPayer(Transaction $transaction): array
    {
        $result = [];

        $profile = $transaction->getProfile();
        if ($profile && $email = $profile->getEmail()) {
            $result['email_address'] = $email;

            if ($address = $profile->getBillingAddress()) {
                $result['name'] = [
                    'given_name' => $address->getFirstname(),
                    'surname'    => $address->getLastname(),
                ];
            }
        }

        return $result;
    }

    protected function getPurchaseUnits(Transaction $transaction): array
    {
        $result = [];

        if ($purchaseUnit = $this->getPurchaseUnit($transaction)) {
            $result[] = $purchaseUnit;
        }

        return $result;
    }

    /**
     * @param Transaction $transaction
     *
     * @return array
     */
    protected function getPurchaseUnit(Transaction $transaction): array
    {
        $result = [];

        $currecy     = $transaction->getCurrency();
        $currecyCode = $currecy->getCode();
        $total       = $currecy->roundValue($transaction->getValue());

        $order = $transaction->getOrder();

        $items      = [];
        $itemsTotal = 0;
        foreach ($order->getItems() as $orderItem) {
            $item = [];

            $item['sku']  = $orderItem->getSku();
            $item['name'] = $orderItem->getName();

            $quantity         = $orderItem->getAmount();
            $item['quantity'] = $quantity;

            $itemValue = $currecy->roundValue($orderItem->getItemNetPrice());

            $item['unit_amount'] = [
                'currency_code' => $currecyCode,
                'value'         => $itemValue,
            ];

            $itemsTotal += $itemValue * $quantity;

            $items[] = $item;
        }

        $shippingValue = $this->getShippingValue($order);

        $taxValue = $this->getTaxValue($order);

        $discountValue = $this->getDisacountValue($order);

        $result['amount'] = [
            'currency_code' => $currecyCode,
            'value'         => $total,
        ];

        if ($total === $itemsTotal + $shippingValue + $taxValue - $discountValue) {
            $result['amount']['breakdown'] = [
                'item_total' => [
                    'currency_code' => $currecyCode,
                    'value'         => $currecy->roundValue($itemsTotal),
                ],
                'shipping'   => [
                    'currency_code' => $currecyCode,
                    'value'         => $shippingValue,
                ],
                'tax_total'  => [
                    'currency_code' => $currecyCode,
                    'value'         => $taxValue,
                ],
                'discount'   => [
                    'currency_code' => $currecyCode,
                    'value'         => $discountValue,
                ],
            ];

            $result['items'] = $items;
        }

        $result['reference_id'] = $transaction->getPublicId();
        $result['custom_id']    = $transaction->getPublicId();
        $result['invoice_id']   = $transaction->getPublicId();

        if ($order->isShippable() && $transaction->getProfile()) {
            $address = $transaction->getProfile()->getShippingAddress();

            if ($address) {
                $result['shipping'] = [
                    'name'    => [
                        'full_name' => $address->getFirstname() . ' ' . $address->getLastname(),
                    ],
                    'address' => [
                        'address_line_1' => $address->getStreet(),
                        'admin_area_1'   => $address->getState()->getCode(),
                        'admin_area_2'   => $address->getCity(),
                        'postal_code'    => $address->getZipcode(),
                        'country_code'   => $address->getCountryCode(),
                    ],
                ];
            }
        }

        return $result;
    }

    /**
     * @param Order $order
     *
     * @return float
     */
    protected function getShippingValue($order): float
    {
        /** @var Modifier|AModifier $shippingModifier */
        $shippingModifier = $order->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');

        if ($shippingModifier && $shippingModifier->canApply()) {
            return $order->getCurrency()->roundValue(
                $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_SHIPPING)
            );
        }

        return 0;
    }

    /**
     * @param Order $order
     *
     * @return float
     */
    protected function getTaxValue($order): float
    {
        return $order->getCurrency()->roundValue(
            $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_TAX)
        );
    }

    /**
     * @param Order $order
     *
     * @return float
     */
    protected function getDisacountValue($order): float
    {
        return $order->getCurrency()->roundValue(
            $order->getSurchargeSumByType(\XLite\Model\Base\Surcharge::TYPE_DISCOUNT)
        );
    }
}
