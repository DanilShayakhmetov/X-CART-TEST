<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View;

use Includes\Utils\Module\Manager;
use XLite\Module\CDev\Paypal;
use XLite\Module\CDev\Paypal\View\FormField\Select\PPCMBannerType;

/**
 * Paypal credit messaging banner
 */
class PPCMBanner extends \XLite\View\AView
{
    const PARAM_PAGE = 'page';

    const PAGE_PRODUCT      = 'product';
    const PAGE_CART         = 'cart';
    const PAGE_CHECKOUT     = 'checkout';
    const PAGE_OPC_CHECKOUT = 'opc_checkout';
    const PAGE_FLC_CHECKOUT = 'flc_checkout';

    /**
     * Get css files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/CDev/Paypal/ppcm_banner/style.less';

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/CDev/Paypal/ppcm_banner/controller.js';

        return $list;
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
            static::PARAM_PAGE => new \XLite\Model\WidgetParam\TypeString('Page', ''),
        ];
    }

    /**
     * Get payment method
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getMethod()
    {
        return Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_PC);
    }

    /**
     * Get Paypal credit setting
     *
     * @param string $name Setting name
     *
     * @return string
     */
    protected function getSetting($name)
    {
        return $this->getMethod()->getSetting($name);
    }

    /**
     * @return string
     */
    protected function getBannerAttributes()
    {
        $attributes = [
            'data-pp-placement'    => $this->getPlacementValue(),
            'data-pp-style-layout' => $this->getSetting('ppcm_banner_type'),
        ];

        $textStyleAttributes = [
            'data-pp-style-logo-type'     => $this->getSetting('ppcm_text_logo_type'),
            'data-pp-style-logo-position' => $this->getSetting('ppcm_text_logo_position'),
            'data-pp-style-text-size'     => $this->getSetting('ppcm_text_size'),
            'data-pp-style-text-color'    => $this->getSetting('ppcm_text_color'),
        ];

        $flexStyleAttributes = [
            'data-pp-style-color' => $this->getSetting('ppcm_flex_color_scheme'),
            'data-pp-style-ratio' => $this->getSetting('ppcm_flex_layout'),
        ];

        if (PPCMBannerType::PPCM_FLEX === $this->getSetting('ppcm_banner_type')) {
            $attributes = array_merge($attributes, $flexStyleAttributes);
        } else {
            $attributes = array_merge($attributes, $textStyleAttributes);
        }

        $result = '';
        foreach ($attributes as $attr => $value) {
            $result .= ' ' . $attr;

            if ('' !== $value) {
                $result .= '="' . $value . '"';
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getPlacementValue()
    {
        switch ($this->getParam(static::PARAM_PAGE)) {
            case static::PAGE_PRODUCT:
                return 'product';

            case static::PAGE_CART:
                return 'cart';

            case static::PAGE_CHECKOUT:
            case static::PAGE_OPC_CHECKOUT:
            case static::PAGE_FLC_CHECKOUT:
                return 'payment';

            default:
                return '';
        }
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $result = parent::isVisible()
            && Paypal\Main::isPaypalCreditForCommercePlatformEnabled()
            && $this->getSetting('ppcm_enabled');

        $isFlcEnabled = Manager::getRegistry()->isModuleEnabled('XC-FastLaneCheckout')
            && \XLite\Module\XC\FastLaneCheckout\Main::isFastlaneEnabled();

        switch ($this->getParam(static::PARAM_PAGE)) {
            case static::PAGE_PRODUCT:
                return $result && $this->getSetting('ppcm_product_page');

            case static::PAGE_CART:
                return $result && $this->getSetting('ppcm_cart_page');

            case static::PAGE_CHECKOUT:
                return $result && $this->getSetting('ppcm_checkout_page');

            case static::PAGE_OPC_CHECKOUT:
                return $result
                    && $this->getSetting('ppcm_checkout_page')
                    && !$isFlcEnabled;

            case static::PAGE_FLC_CHECKOUT:
                return $result
                    && $this->getSetting('ppcm_checkout_page')
                    && $isFlcEnabled;

            default:
                return false;
        }
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/ppcm_banner/body.twig';
    }
}
