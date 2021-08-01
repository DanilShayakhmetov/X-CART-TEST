<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;


use XLite\Model\Payment\Transaction;
use XLite\Model\Payment\TransactionData;

class TransactionDetails extends \XLite\View\AView
{
    const PARAM_TRANSACTION = 'transaction';

    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getTransaction()
            && $this->hasData();
    }

    /**
     * @return bool
     */
    protected function hasData()
    {
        return $this->getDetails() || $this->getItems();
    }

    protected function getCommonFiles()
    {
        return array_merge_recursive(parent::getCommonFiles(), [
            static::RESOURCE_CSS => [
                'transaction_details/style.less',
            ],
        ]);
    }

    protected function getDefaultTemplate()
    {
        return 'transaction_details/body.twig';
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_TRANSACTION => new \XLite\Model\WidgetParam\TypeObject(
                'Transaction', null
            ),
        ];
    }

    /**
     * @return Transaction
     */
    protected function getTransaction()
    {
        return $this->getParam(static::PARAM_TRANSACTION);
    }

    /**
     * @return array
     */
    protected function getDetails()
    {
        $data = $this->getTransaction()->getData();

        $data = array_combine(array_map(function (TransactionData $datum) {
            return $datum->getName();
        }, $data->toArray()), $data->toArray());

        unset($data['cart_items']);

        return $data;
    }

    /**
     * @return array
     */
    protected function getItems()
    {
        $transaction = $this->getTransaction();

        if ($items = $transaction->getDetail('cart_items')) {
            return @unserialize($items) ?: [];
        }

        return $transaction->getCartItems();
    }

    /**
     * @return float
     */
    protected function getTotal()
    {
        $transaction = $this->getTransaction();

        return $transaction
            ? $transaction->getValue()
            : 0;
    }
}