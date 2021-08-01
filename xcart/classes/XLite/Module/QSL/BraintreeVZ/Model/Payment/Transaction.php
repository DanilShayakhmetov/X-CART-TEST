<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\BraintreeVZ\Model\Payment;

/**
 * Braintree payment processor
 *
 */
class Transaction extends \XLite\Model\Payment\Transaction implements \XLite\Base\IDecorator
{
    /**
     * Prefix in transaction details
     */
    const BRAINTREE_PREFIX = '[BRAINTREE]';
    const BRAINTREE_CREDIT_CARD_PREFIX = '[Credit card] ';
    const BRAINTREE_PAYPAL_PREFIX = '[PayPal] ';
    const BRAINTREE_LITERAL_FIELD_PREFIX = 'literal_';
    const BRAINTREE_LITERAL_TITLE_POSTFIX = ' (literal)';
   
    const BRAINTREE_PROCESSED_FLAG = 'braintree_processed_flag';
 
    /**
     * Save datacell in transaction
     *
     * @param $field Field name
     * @param $value Value
     * @param $title Title
     *
     * @return void 
     */
    public function setBraintreeDataCell($field, $value, $title, $fieldPrefix = '', $titlePrefix = '', $titlePostfix = '')
    {
        $field = static::BRAINTREE_PREFIX . $fieldPrefix . $field;

        $title = $titlePrefix . $title . $titlePostfix;

        if ($value instanceof \DateTime) {
            $value->setTimezone(\XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::getInstance()->getStoreTimeZone());
            $value = $value->format('Y-m-d H:i:s');
        }

        $this->setDataCell($field, $value, $title, 'C');
    }

    /**
     * Save literal datacell in transaction
     *
     * @param $field Field name
     * @param $value Value
     * @param $title Title
     *
     * @return void
     */
    public function setBraintreeLiteralDataCell($field, $value, $title)
    {
        $this->setBraintreeDataCell(
            $field, 
            $value, 
            $title, 
            static::BRAINTREE_LITERAL_FIELD_PREFIX,
            '', 
            static::BRAINTREE_LITERAL_TITLE_POSTFIX
        );
    }

    /**
     * Save credit card datacell in transaction
     *
     * @param $field Field name
     * @param $value Value
     * @param $title Title
     *
     * @return void
     */
    public function setBraintreeCreditCardDataCell($field, $value, $title)
    {
        $this->setBraintreeDataCell(
            $field, 
            $value, 
            $title, 
            '', 
            static::BRAINTREE_CREDIT_CARD_PREFIX
        );
    }

    /**
     * Save PayPal datacell in transaction
     *
     * @param $field Field name
     * @param $value Value
     * @param $title Title
     *
     * @return void
     */
    public function setBraintreePayPalDataCell($field, $value, $title)
    {
        $this->setBraintreeDataCell(
            $field,
            $value,
            $title,
            '',
            static::BRAINTREE_PAYPAL_PREFIX
        );
    }

    /**
     * Mark transaction as processed to prevent duplicates 
     *
     * @return void
     */
    public function markBraintreeProcessed()
    {
        if (!$this->getDataCell(static::BRAINTREE_PROCESSED_FLAG)) {
            $this->setDataCell(static::BRAINTREE_PROCESSED_FLAG, '1', '', 'C');

            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Is transaction processed by Braintree
     *
     * @return void
     */
    public function isBraintreeProcessed()
    {
        return $this->getDataCell(static::BRAINTREE_PROCESSED_FLAG)
            && $this->getDataCell(static::BRAINTREE_PROCESSED_FLAG)->getValue();
    }

    /**
     * Is transaction processed by Braintree
     *
     * @return void
     */
    public function isBraintreeTransaction()
    {
        return $this->getPaymentMethod()
            && \XLite\Module\QSL\BraintreeVZ\Core\BraintreeClient::BRAINTREE_CLASS == $this->getPaymentMethod()->getClass();
    }

    /**
     * Get Braintree data cell object by name
     *
     * @param string $name Name of data cell
     *
     * @return \XLite\Model\Payment\TransactionData
     */
    public function getBraintreeDataCell($name)
    {
        $name = static::BRAINTREE_PREFIX . $name;

        return parent::getDataCell($name);
    }

    /**
     * Get details which should not be displayed for users
     *
     * @return array
     */
    public static function getDetailsExcludeKeys()
    {
        return array(
            static::BRAINTREE_CREDIT_CARD_PREFIX,
            static::BRAINTREE_PREFIX . 'imageUrl',
            static::BRAINTREE_PROCESSED_FLAG,
        );
    }

    /**
     * Get list of transaction data matched to the data list defined in processor
     * Return processor-specific data or (of it is empty and not strict mode) all stored data
     *
     * @param boolean $strict Strict flag
     *
     * @return array
     */
    public function getTransactionData($strict = false)
    {
        $list = parent::getTransactionData($strict);

        if ($this->isBraintreeTransaction() && !$strict) {
            foreach ($list as $key => $cell) {
                if (in_array($cell->getName(), static::getDetailsExcludeKeys())) {
                    unset($list[$key]);
                }
            }
        }

        return $list;
    }
}
