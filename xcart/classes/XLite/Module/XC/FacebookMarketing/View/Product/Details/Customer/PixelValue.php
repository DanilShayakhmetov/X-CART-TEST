<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\View\Product\Details\Customer;

/**
 * @ListChild (list="product.details.page.info", weight="5")
 * @ListChild (list="product.details.quicklook.info", weight="5")
 */
class PixelValue extends \XLite\View\Product\Details\Customer\Widget
{
    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-pixel-value';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/FacebookMarketing/product/details/pixel_value/body.twig';
    }

    /**
     * @return string
     */
    protected function getFacebookPixelValue()
    {
        $price = $this->getProduct()->getNetPrice();

        $valuePercentage = (float) \XLite\Core\Config::getInstance()->XC->FacebookMarketing->view_content_value;

        return \XLite::getInstance()->getCurrency()->roundValue($price * ($valuePercentage / 100));
    }

    /**
     * @return string
     */
    protected function getFacebookPixelValueCurrency()
    {
        return \XLite::getInstance()->getCurrency()->getCode();
    }

    /**
     * @return string
     */
    protected function getFacebookPixelContentId()
    {
        return $this->getProduct()->getFacebookPixelProductIdentifier();
    }
}
