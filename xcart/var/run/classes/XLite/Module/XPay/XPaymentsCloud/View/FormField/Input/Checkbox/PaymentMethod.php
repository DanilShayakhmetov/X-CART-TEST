<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\FormField\Input\Checkbox;

use \XLite\Module\XPay\XPaymentsCloud\Main as ModuleMain;

 class PaymentMethod extends \XLite\View\FormField\Input\Checkbox\PaymentMethodAbstract implements \XLite\Base\IDecorator
{
   /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();

        $applePayMethodId = ModuleMain::getApplePayMethod()->getMethodId();

        if ($this->getParam(static::PARAM_METHOD_ID) == $applePayMethodId) {
            $xpaymentsMethodId = ModuleMain::getPaymentMethod()->getMethodId();
            $list['data-tremble-button'] = sprintf('#payment-id-%s', $xpaymentsMethodId);
        }

        return $list;
    }
}
