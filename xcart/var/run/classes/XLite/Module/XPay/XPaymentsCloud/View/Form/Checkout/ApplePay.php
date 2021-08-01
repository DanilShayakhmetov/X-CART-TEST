<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Form\Checkout;

/**
 * Place order form
 */
class ApplePay extends \XLite\View\Form\Checkout\ACheckout
{
    /**
     * Widget parameter names
     */
    const PARAM_BUY_MODE = 'buyWithApplePay';

    /**
     * JavaScript: this value will be returned on form submit
     * NOTE - this function designed for AJAX easy switch on/off
     *
     * @return string
     */
    protected function getOnSubmitResult()
    {
        return 'false';
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'xpayments_apple_pay_checkout';
    }

    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'checkout';
    }

    /**
     * initView
     *
     * @return void
     */
    protected function initView()
    {
        parent::initView();

        $this->widgetParams[self::PARAM_FORM_PARAMS]->appendValue($this->getFormDefaultParams());
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
            self::PARAM_BUY_MODE => new \XLite\Model\WidgetParam\TypeBool('Buy With Apple Pay mode', false, true)
        );
    }

    /**
     * getFormDefaultParams
     *
     * @return array
     */
    protected function getFormDefaultParams()
    {
        return [
            'xpaymentsBuyWithApplePay' => $this->getParam(self::PARAM_BUY_MODE) ? '1' : '',
        ];
    }

}
