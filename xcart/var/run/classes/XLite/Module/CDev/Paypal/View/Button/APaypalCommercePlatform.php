<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Button;

/**
 * Paypal Commerce Platform base button
 */
abstract class APaypalCommercePlatform extends \XLite\View\Button\Link
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/CDev/Paypal/button/paypal_commerce_platform/button.js',
            'modules/CDev/Paypal/button/js/cart.js',
        ]);
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'modules/CDev/Paypal/button/paypal_commerce_platform/style.less',
        ]);
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        $cart = \XLite::getController()->getCart();

        return parent::isVisible() && \XLite\Module\CDev\Paypal\Main::isPaypalCommercePlatformEnabled($cart);
    }

    /**
     * @return string|null
     */
    protected function getDefaultLocation()
    {
        return $this->buildURL('paypal_commerce_platform', 'create_order');
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/button/paypal_commerce_platform/button.twig';
    }

    /**
     * @return string
     */
    protected function getButtonClass()
    {
        return 'button pp-button pcp-button-container' . ' pp-' . $this->getButtonSize();
    }

    protected function getButtonParams()
    {
        $result = [
            'layout'  => $this->getButtonLayout(),
            'color'   => $this->getButtonColor(),
            'shape'   => $this->getButtonShape(),
            'label'   => $this->getButtonLabel(),
            'height'  => $this->getButtonHeight(),
        ];

        if ($result['layout'] !== 'vertical') {
            $result['tagline'] = $this->getButtonTagline();
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getButtonStyleNamespace()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function getButtonLayout()
    {
        return 'vertical';
    }

    /**
     * @return string
     */
    protected function getButtonSize()
    {
        $configVariable = $this->getButtonStyleNamespace() . '_style_size';

        return \XLite\Core\Config::getInstance()->CDev->Paypal->{$configVariable} ?: 'responsive';
    }

    /**
     * @return string
     */
    protected function getButtonColor()
    {
        $configVariable = $this->getButtonStyleNamespace() . '_style_color';

        return \XLite\Core\Config::getInstance()->CDev->Paypal->{$configVariable} ?: 'gold';
    }

    /**
     * @return string
     */
    protected function getButtonShape()
    {
        $configVariable = $this->getButtonStyleNamespace() . '_style_shape';

        return \XLite\Core\Config::getInstance()->CDev->Paypal->{$configVariable} ?: 'rect';
    }

    /**
     * paypal (Recommended), checkout, buynow, pay, installment
     *
     * @return string
     */
    protected function getButtonLabel()
    {
        return 'paypal';
    }

    /**
     * @return string
     */
    protected function getButtonTagline()
    {
        return 'true';
    }

    /**
     * @return int
     */
    protected function getButtonHeight()
    {
        return 40;
    }
}
