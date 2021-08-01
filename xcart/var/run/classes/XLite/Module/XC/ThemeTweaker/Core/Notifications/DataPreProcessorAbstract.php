<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core\Notifications;


use XLite\Core\Converter;
use XLite\Model\Order;
use XLite\Model\Payment\Transaction;
use XLite\Model\Product;

abstract class DataPreProcessorAbstract
{
    /**
     * @param string $dir
     * @param array  $data
     *
     * @return array
     */
    public static function prepareDataForNotification($dir, array $data)
    {
        switch ($dir) {
            case 'low_limit_warning':
                $data = static::prepareLowLimitWarningData($data);
                break;
            case 'order_tracking_information':
                $data = static::prepareOrderTrackingInformationData($data);
                break;
            case 'failed_transaction':
                $data = static::prepareFailedTransactionData($data);
                break;
            case 'backorder_created':
                $data = static::prepareBackorderCreatedData($data);
                break;
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected static function prepareLowLimitWarningData(array $data)
    {
        if (
            !empty($data['product'])
            && $data['product'] instanceof Product
        ) {
            $data['product'] = $data['product']->prepareDataForNotification();
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected static function prepareOrderTrackingInformationData(array $data)
    {
        if (
            !empty($data['order'])
            && $data['order'] instanceof Order
        ) {
            $data['trackingNumbers'] = $data['order']->getTrackingNumbers();
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected static function prepareFailedTransactionData(array $data)
    {
        if (
            !empty($data['order'])
            && ($order = $data['order']) instanceof Order
        ) {
            /* @var Order $order */
            if ($transaction = $order->getPaymentTransactions()->last()) {
                /* @var Transaction $transaction */
                $data = [
                        'transaction'          => $transaction,
                        'transactionSearchURL' => Converter::buildFullURL('payment_transactions', '', [
                            'public_id' => $transaction->getPublicId(),
                        ]),
                    ] + $data;
            }
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected static function prepareBackorderCreatedData(array $data)
    {
        if (
            !empty($data['order'])
            && ($order = $data['order']) instanceof Order
        ) {
            $order = $data['order'];
            \XLite\Core\Database::getEM()->detach($order);
            $data['items'] = array_map(function (\XLite\Model\OrderItem $item) {
                if (0 >= $item->getBackorderedAmount()) {
                    $item->setBackorderedAmount(rand(1, $item->getAmount()));
                }

                return $item;
            }, $order->getItems()->toArray());
        }

        return $data;
    }
}