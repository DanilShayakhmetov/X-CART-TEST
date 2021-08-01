<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Module\XC\ProductVariants\Model;

/**
 * The "product" model class
 *
 * @Decorator\Depend("XC\ProductVariants")
 * @Decorator\After("XC\FacebookMarketing")
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Return product identifier for facebook pixel
     *
     * @return string
     */
    public function getFacebookPixelProductIdentifier()
    {
        $result = parent::getSku();

        if ($this->hasVariants() && $variant = $this->getDefaultVariant()) {
            $result = $variant->getSku() ?: $variant->getVariantId();
        }

        return $result;
    }
}