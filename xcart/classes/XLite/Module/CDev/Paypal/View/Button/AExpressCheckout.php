<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Button;

/**
 * Express Checkout base button
 */
abstract class AExpressCheckout extends \XLite\View\Button\Link
{
    const PARAM_IN_CONTEXT = 'inContext';

    /**
     * @return boolean
     */
    protected function isVisible()
    {
        $cart = $this->getCart();

        return parent::isVisible() && \XLite\Module\CDev\Paypal\Main::isExpressCheckoutEnabled($cart);
    }

    /**
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[static::PARAM_LOCATION] = new \XLite\Model\WidgetParam\TypeString(
            'Redirect to',
            $this->buildURL('checkout', 'start_express_checkout')
        );
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/button/ec_button.twig';
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/CDev/Paypal/checkout.js',
            'modules/CDev/Paypal/button/js/button.js',
            'modules/CDev/Paypal/button/js/cart.js',
        ]);
    }

    /**
     * @return string
     */
    protected function getButtonClass()
    {
        return 'button pp-button pp-express-checkout-button';
    }

    /**
     * @return array
     */
    protected function getAllowedLocales()
    {
        return [
            'en'    => 'en_US',
            'au'    => 'en_AU',
            'gb'    => 'en_GB',
            'fr_CA' => 'fr_CA',
            'es'    => 'es_ES',
            'it'    => 'it_IT',
            'fr'    => 'fr_FR',
            'de'    => 'de_DE',
            'br'    => 'pt_BR',
            'zh'    => 'zh_CN',
            'da'    => 'da_DK',
            'zh_HK' => 'zh_HK',
            'id'    => 'id_ID',
            'he'    => 'he_IL',
            'ja'    => 'ja_JP',
            'nl'    => 'nl_NL',
            'no'    => 'no_NO',
            'pl'    => 'pl_PL',
            'pt'    => 'pt_PT',
            'ru'    => 'ru_RU',
            'se'    => 'sv_SE',
            'th'    => 'th_TH',
            'zh_TW' => 'zh_TW',
            'ar'    => 'ar_EG',
            'fr_XC' => 'fr_XC',
            'es_XC' => 'es_XC',
            'zh_XC' => 'zh_XC',
            'ko'    => 'ko_KR',
        ];
    }

    /**
     * @return null|string
     */
    protected function getLocale()
    {
        $locale = @mb_substr(\XLite\Core\Converter::getLocaleByCode(), 0, 5);

        if (in_array($locale, $this->getAllowedLocales())) {
            return $locale;
        }

        $code = \XLite\Core\Session::getInstance()->getLanguage()->getCode();

        return isset($this->getAllowedLocales()[$code]) ? $this->getAllowedLocales()[$code] : null;
    }

    /**
     * @return array
     */
    protected function getButtonAdditionalParams()
    {
        $result = [];

        if ($this->getLocale()) {
            $result['data-locale'] = $this->getLocale();
        }

        $result['data-style-layout'] = $this->getButtonLayout();
        $result['data-style-size']   = $this->getButtonSize();
        $result['data-style-color']  = $this->getButtonColor();
        $result['data-style-shape']  = $this->getButtonShape();

        return $result;
    }

    /**
     * @return string
     */
    protected function getButtonAdditionalParamsCode()
    {
        return implode(' ', array_map(function ($k, $v) {
            return "{$k}=\"{$v}\"";
        }, array_keys($this->getButtonAdditionalParams()), $this->getButtonAdditionalParams()));
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
}
