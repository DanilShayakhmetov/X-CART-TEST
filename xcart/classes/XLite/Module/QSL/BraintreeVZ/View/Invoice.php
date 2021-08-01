<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\View;

/**
 * Braintree credit card 
 */
class Invoice extends \XLite\View\Invoice implements \XLite\Base\IDecorator
{
    /**
     * Braintree tranaction
     */
    protected $braintreeTransaction = null;

    /**
     * Get skin directory
     *
     * @return string
     */
    protected static function getDirectory()
    {
        return 'modules/QSL/BraintreeVZ/invoice';
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = self::getDirectory() . '/style.css';

        return $list;
    }

    /**
     * Get Braintree transaction if any
     *
     * @return \XLite\Model\Payment\Transaction or null
     */
    protected function getBraintreeTransaction()
    {
        if (!$this->braintreeTransaction) {

            $transactions = $this->getOrder()->getPaymentTransactions();

            foreach ($transactions as $transaction) {
                if (
                    $transaction->isBraintreeProcessed()
                    && $transaction->isCompleted()
                    && $transaction->getBraintreeDataCell('bin')
                    && $transaction->getBraintreeDataCell('last4')
                    && $transaction->getBraintreeDataCell('cardType')
                    && $transaction->getBraintreeDataCell('imageUrl')
                ) {

                    $this->braintreeTransaction = $transaction;

                    break;
                }
            }

        }

        return $this->braintreeTransaction;
    }

    /**
     * Get credit card logo URL
     *
     * @return string
     */
    protected function getBraintreeCardLogo()
    {
        return $this->getBraintreeTransaction()
            ? $this->getBraintreeTransaction()->getBraintreeDataCell('imageUrl')->getValue()
            : '';
    }

    /**
     * Get credit card type
     *
     * @return string
     */
    protected function getBraintreeCardType()
    {
        return $this->getBraintreeTransaction()
            ? $this->getBraintreeTransaction()->getBraintreeDataCell('cardType')->getValue()
            : '';
    }

    /**
     * Get masked credit card number
     *
     * @return string
     */
    protected function getBraintreeCardNumber()
    {
        if ($this->getBraintreeTransaction()) {

            $result = $this->getBraintreeTransaction()->getBraintreeDataCell('bin')->getValue()
                . '******'
                . $this->getBraintreeTransaction()->getBraintreeDataCell('last4')->getValue();

        } else {

            $result = '';
        }

        return $result;
    }
}
