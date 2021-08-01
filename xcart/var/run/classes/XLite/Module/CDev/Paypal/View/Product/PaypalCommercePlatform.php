<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Product;

/**
 * Class ExpressCheckoutButton
 */
class PaypalCommercePlatform extends \XLite\View\Product\Details\Customer\Widget
{
    protected function isVisible()
    {
        return parent::isVisible() && \XLite\Module\CDev\Paypal\Main::isPaypalCommercePlatformEnabled($this->getCart());
    }

    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-product-paypal-pcp-button';
    }

    /**
     * Return directory contains the template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Paypal/product/details/paypal_commerce_platform_widget.twig';
    }
}