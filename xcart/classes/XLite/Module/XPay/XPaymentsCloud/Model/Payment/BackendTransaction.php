<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Model\Payment;

class BackendTransaction extends \XLite\Model\Payment\BackendTransaction implements \XLite\Base\IDecorator
{
    /**
     * @return boolean
     */
    public function hasCustomAmount()
    {
        $result = parent::hasCustomAmount();

        if ($this->getPaymentTransaction()->isXpayments()) {
            $result = $result
                || in_array($this->getType(), [
                    static::TRAN_TYPE_CAPTURE_PART,
                    static::TRAN_TYPE_CAPTURE_MULTI,
                ]);
        }

        return $result;
    }

    /**
     * @param $amount
     *
     * @return $this
     * @throws \XLite\Core\Exception\IncorrectValueException
     */
    public function setCustomAmountCapturePart($amount)
    {
        if ($amount > 0 && $amount <= $this->getMaxCaptureAmount()) {
            $this->setValue($amount);
        } else {
            throw new \XLite\Core\Exception\IncorrectValueException('Incorrect amount');
        }

        return $this;
    }

    /**
     * @param $amount
     *
     * @return $this
     * @throws \XLite\Core\Exception\IncorrectValueException
     */
    public function setCustomAmountCaptureMulti($amount)
    {
        return $this->setCustomAmountCapturePart($amount);
    }

    /**
     * Return max amount to refund
     *
     * @return float
     */
    public function getMaxCaptureAmount()
    {
        $currency = $this->getPaymentTransaction()->getCurrency() ?: $this->getPaymentTransaction()->getOrder()->getCurrency();
        return $currency->roundValue($this->getPaymentTransaction()->getChargeValueModifier());
    }

    /**
     * Check if the backend transaction is of accept type
     *
     * @return boolean
     */
    public function isAccept()
    {
        return self::TRAN_TYPE_ACCEPT == $this->getType();
    }

    /**
     * Check if the backend transaction is of decline type
     *
     * @return boolean
     */
    public function isDecline()
    {
        return self::TRAN_TYPE_DECLINE == $this->getType();
    }

}
