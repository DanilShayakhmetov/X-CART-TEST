<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Button\Product;

/**
 * Buy With Apple Pay button
 */
class BuyWithApplePay extends \XLite\Module\XPay\XPaymentsCloud\View\Button\ACheckoutWithApplePay
{
    /**
     * Widget parameter name
     */
    const PARAM_PRODUCT = 'product';

    /**
     * Return list of required JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XPay/XPaymentsCloud/button/buy_apple_pay.js';

        return $list;
    }

    /**
     * @return string
     */
    protected function getButtonClass()
    {
        return parent::getButtonClass() . ' apple-pay-buy-button';
    }

    /**
     * Returns button label for old devices
     *
     * @return string
     */
    protected function getButtonLabel()
    {
        return 'Buy with';
    }

    /**
     * It is used to indicate it is Buy or Checkout button
     *
     * @return string
     */
    protected function getButtonMode()
    {
        return 'buy';
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XPay/XPaymentsCloud/button/buy_apple_pay.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, 'XLite\Model\Product'),
        ];
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(static::PARAM_PRODUCT);
    }

}
