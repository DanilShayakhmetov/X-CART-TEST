<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order\Details\Admin;


use XLite\Model\Order;
use XLite\Model\Payment\Transaction;

class TransactionDetails extends \XLite\View\AView
{
    const PARAM_ORDER = 'order';

    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getTransaction()
            && $this->isDisplayForOrder()
            && !$this->getTransaction()->getTransactionData()->isEmpty();
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'order/transaction_details/style.less',
        ]);
    }

    /**
     * @param Order $order
     *
     * @return bool
     */
    protected function isDisplayForOrder()
    {
        return $this->getOrder()->getPaymentStatusCode() === Order\Status\Payment::STATUS_DECLINED
            || $this->getOrder()->getShippingStatusCode() === Order\Status\Shipping::STATUS_WAITING_FOR_APPROVE;
    }

    protected function getDefaultTemplate()
    {
        return 'order/transaction_details/body.twig';
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_ORDER => new \XLite\Model\WidgetParam\TypeObject('Order', null, false, 'XLite\Model\Payment\Transaction'),
        ];
    }

    /**
     * @return null|Order
     */
    protected function getOrder()
    {
        $order = $this->getParam(static::PARAM_ORDER);

        return $order && $order instanceof Order
            ? $order
            : null;
    }

    /**
     * @return null|Transaction
     */
    protected function getTransaction()
    {
        return $this->getOrder()
            ? $this->getOrder()->getPaymentTransactions()->last()
            : null;
    }

    /**
     * @return string
     */
    protected function getTransactionStatusPopoverContent()
    {
        return $this->getWidget(
            $this->getTooltipWidgetParams(),
            '\XLite\View\FailedTransactionTooltip'
        )
            ->getContent();
    }

    /**
     * @return array
     */
    protected function getTooltipWidgetParams()
    {
        $params = [
            'entity' => $this->getTransaction(),
        ];

        if ($this->isDisplayFailedTransactionTitle()) {
            $params['title'] = static::t('Transaction was failed');
        }

        return $params;
    }

    /**
     * @return bool
     */
    protected function isDisplayFailedTransactionTitle()
    {
        return $this->getOrder()->getPaymentStatusCode() === Order\Status\Payment::STATUS_DECLINED;
    }

    /**
     * @return string
     */
    protected function getTransactionStatusPopoverTitle()
    {
        return static::t('Details');
    }
}