<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Order\Details\Admin;

/**
 * Payment actions unit widget extension to allow partial capture
 *
 */
 class PaymentActionsUnit extends \XLite\View\Order\Details\Admin\PaymentActionsUnitAbstract implements \XLite\Base\IDecorator
{
    /**
     * Payment action units that need amount
     *
     * @return array
     */
    protected function needAmount()
    {
        $result = parent::needAmount();

        if ($this->getParam(self::PARAM_TRANSACTION)->isXpayments()) {
            if (!in_array(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_PART, $result)) {
            $result[] = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_PART;
            }
            if (!in_array(\XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_MULTI, $result)) {
                $result[] = \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_MULTI;
            }
        }

        return $result;
    }

    /**
     * Button widget class
     * 
     * @return string
     */
    protected function getButtonWidgetClass(){

        $class = parent::getButtonWidgetClass();

        if ($this->getParam(self::PARAM_TRANSACTION)->isXpayments()) {

            $captureActions = [
                \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_PART,
                \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_CAPTURE_MULTI
            ];

            if (
                in_array($this->getParam(self::PARAM_UNIT), $this->needAmount())
                && in_array($this->getParam(self::PARAM_UNIT), $captureActions)
            ) {
                $class = 'XLite\Module\XPay\XPaymentsCloud\View\FormField\Input\CaptureMultiple';
            }

        }

        return $class;
    }

}
