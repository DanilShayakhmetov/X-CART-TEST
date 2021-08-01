<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\Model\Payment;

use \XLite\Module\XPay\XPaymentsCloud\Main as XPaymentsCloud;

/**
 * Payment method
 */
class Method extends \XLite\Model\Payment\Method implements \XLite\Base\IDecorator
{
    /**
    * Get method_id
    *
    * @return integer
    */
    public function getMethodId()
    {
        return (
            $this->isLegacyXpaymentsMethod()
            && XPaymentsCloud::getPaymentMethod()
            && $this->getFromMarketplace()
        )
            ? XPaymentsCloud::getPaymentMethod()->getMethodId()
            : parent::getMethodId();
    }

    /**
     * Get added
     *
     * @return bool
     */
    public function getAdded()
    {
        $result = parent::getAdded();

        if ($this->isLegacyXpaymentsMethod()) {
            if (XPaymentsCloud::getPaymentMethod()->getAdded()) {
                $result = true;
            } else {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Call custom handler when adding/removing payment method
     *
     * @param boolean $added Property value
     *
     * @return \XLite\Model\Payment\Method
     */
    public function setAdded($added)
    {
        $result = parent::setAdded($added);

        if (XPaymentsCloud::XPAYMENTS_SERVICE_NAME == $this->getServiceName()) {
            if ($added) {
                XPaymentsCloud::onAddPaymentMethod($this);
            } else {
                XPaymentsCloud::onRemovePaymentMethod($this);
            }
        }

        return $result;
    }

    /**
     * Returns true if it is an old X-Payments payment method
     *
     * @return bool
     */
    public function isLegacyXpaymentsMethod()
    {
        return false !== strpos($this->getServiceName(), 'XPayments.Allowed')
            || false !== strpos($this->getServiceName(), 'SavedCard');
    }

    /**
     * Get warning note
     *
     * @return string
     */
    public function getWarningNote()
    {
        if (
            XPaymentsCloud::APPLE_PAY_SERVICE_NAME == $this->getServiceName()
            && $this->getProcessor()
        ) {
            $message = $this->getProcessor()->getWarningNote($this);
        }

        if (empty($message)) {
            $message = parent::getWarningNote();
        }

        return $message;
    }

    /**
     * Returns true if it is X-Payments or Apple Pay payment method
     *
     * @return bool
     */
    public function isXpayments()
    {
        return in_array(
            $this->getServiceName(),
            array(
                XPaymentsCloud::XPAYMENTS_SERVICE_NAME,
                XPaymentsCloud::APPLE_PAY_SERVICE_NAME
            )
        );
    }

    /**
     * Returns true if it is X-Payments or Apple Pay payment method
     *
     * @return bool
     */
    public function isXpaymentsApplePay()
    {
        return (XPaymentsCloud::APPLE_PAY_SERVICE_NAME == $this->getServiceName());
    }
}
