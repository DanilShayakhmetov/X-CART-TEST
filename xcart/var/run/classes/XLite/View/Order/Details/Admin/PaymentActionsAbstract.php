<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order\Details\Admin;

use Doctrine\Common\Collections\Collection;
use XLite\Model\Payment\BackendTransaction;
use XLite\Model\Payment\Transaction;

/**
 * Payment actions widget (capture, refund, void etc)
 */
abstract class PaymentActionsAbstract extends \XLite\View\AView
{
    /**
     *  Widget parameter names
     */
    const PARAM_ORDER         = 'order';
    const PARAM_UNITS_FILTER  = 'unitsFilter';


    protected $allowedTransactions = null;


    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/payment_actions.twig';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'order/order';
    }


    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ORDER        => new \XLite\Model\WidgetParam\TypeObject('Order', null, false, 'XLite\Model\Order'),
            self::PARAM_UNITS_FILTER => new \XLite\Model\WidgetParam\TypeSet('Units filter', array(), false),
        );
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $result = parent::isVisible()
            && $this->getParam(self::PARAM_ORDER);

        if ($result) {
            $cnt = 0;
            foreach ($this->getTransactions() as $transaction) {
                $cnt += count($this->getTransactionUnits($transaction));
            }

            $result = $cnt > 0;
        }

        return $result;
    }

    // {{{ Content helpers

    /**
    /**
     * Get transactions
     *
     * @return array
     */
    protected function getTransactions()
    {
        $transactions = $this->getParam(self::PARAM_ORDER)->getPaymentTransactions();

        if (count($transactions) > 1) {
            /** @var Collection $transactions */
            $transactions = $transactions->filter(function($transaction) {
                /** @var Transaction $transaction */
                return $transaction->isCompleted();
            });
        }

        return $transactions;
    }

    /*
     * Get order number
     *
     * @return string
     */
    protected function getOrderNumber()
    {
        return $this->getParam(self::PARAM_ORDER)->getOrderNumber();
    }

    /**
     * Get backend transactions
     *
     * @return array
     */
    protected function getBackendTransactions($transaction)
    {
        return $transaction->getBackendTransactions();
    }

    /**
     * Get transaction human-readable status
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     *
     * @return string
     */
    protected function getTransactionStatus(\XLite\Model\Payment\Transaction $transaction)
    {
        return static::t($transaction->getReadableStatus());
    }

    /**
     * Get transaction additional data
     *
     * @param \XLite\Model\Payment\Transaction $transaction Transaction
     *
     * @return array
     */
    protected function getTransactionData(\XLite\Model\Payment\Transaction $transaction)
    {
        $list = array();

        foreach ($transaction->getData() as $cell) {
            if ($cell->getLabel()) {
                $list[] = $cell;
            }
        }

        return $list;
    }

    /**
     * Get list of allowed backend transactions
     *
     * @param \XLite\Model\Payment\Transaction $transaction Payment transaction
     *
     * @return array
     */
    protected function getTransactionUnits($transaction = null)
    {
        if (isset($transaction) && $transaction->getPaymentMethod()) {

            $processor = $transaction->getPaymentMethod()->getProcessor();

            if ($processor) {

                $this->allowedTransactions = $processor->getAllowedTransactions();

                foreach ($this->allowedTransactions as $k => $v) {
                    if (!$processor->isTransactionAllowed($transaction, $v) || !$this->isTransactionFiltered($v)) {
                        unset($this->allowedTransactions[$k]);
                    }
                }

                if (in_array(BackendTransaction::TRAN_TYPE_REFUND_MULTI, $this->allowedTransactions)) {
                    $this->allowedTransactions = array_diff($this->allowedTransactions, [
                        BackendTransaction::TRAN_TYPE_REFUND,
                        BackendTransaction::TRAN_TYPE_REFUND_PART,
                    ]);
                } elseif (in_array(BackendTransaction::TRAN_TYPE_REFUND_PART, $this->allowedTransactions)) {
                    $this->allowedTransactions = array_diff($this->allowedTransactions, [
                        BackendTransaction::TRAN_TYPE_REFUND,
                    ]);
                }
            }
            return $this->allowedTransactions;
        }

        return [];
    }

    /**
     * Returns true if transaction is in filter
     *
     * @param string $transactionType Type of backend transaction
     *
     * @return boolean
     */
    protected function isTransactionFiltered($transactionType)
    {
        $filter = $this->getParam(self::PARAM_UNITS_FILTER);

        return (empty($filter) || in_array($transactionType, $filter));
    }

    /**
     * Returns true if unit is last in the array (for unit separator displaying)
     *
     * @param integer $key Key of unit in the array
     *
     * @return boolean
     */
    protected function isLastUnit($key)
    {
        $keys = array_keys($this->getTransactionUnits());
        return array_pop($keys) == $key;
    }

    // }}}
}

