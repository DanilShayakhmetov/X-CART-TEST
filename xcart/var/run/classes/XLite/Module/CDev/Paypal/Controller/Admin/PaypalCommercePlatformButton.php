<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Admin;

use XLite\Model\Payment\Method;
use XLite\Module\CDev\Paypal\Main as PaypalMain;

class PaypalCommercePlatformButton extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Paypal module string name for payment methods
     */
    const MODULE_NAME = 'CDev_Paypal';

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $paymentMethod = $this->getPaymentMethod();

        return $paymentMethod
            ? $paymentMethod->getName()
            : '';
    }

    /**
     * Return class name for the controller main form
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\Module\CDev\Paypal\View\Model\PaypalButton';
    }

    public function doActionUpdate()
    {
        $list = new \XLite\Module\CDev\Paypal\View\ItemsList\Model\PaypalButton();
        $list->processQuick();

        $this->getModelForm()->performAction('update');
    }

    /**
     * @return Method
     */
    public function getPaymentMethod()
    {
        if (!isset($this->paymentMethod)) {
            $this->paymentMethod = PaypalMain::getPaymentMethod(
                PaypalMain::PP_METHOD_PCP
            );
        }

        return $this->paymentMethod;
    }
}
