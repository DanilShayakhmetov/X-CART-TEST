<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FacebookMarketing\Module\XC\ProductVariants\View\Product\Details\Customer;

/**
 * @Decorator\Depend("XC\ProductVariants")
 * @Decorator\After("XC\FacebookMarketing")
 */
class PixelValue extends \XLite\Module\XC\FacebookMarketing\View\Product\Details\Customer\PixelValue implements \XLite\Base\IDecorator
{
    /**
     * @return string
     */
    protected function getFacebookPixelContentId()
    {
        if (
            $this->getAttributeValues()
            && $variant = $this->getProduct()->getVariant($this->getAttributeValues())
        ) {
            $result = $variant->getSku() ?: $variant->getVariantId();
        } else {
            $result = parent::getFacebookPixelContentId();
        }

        return $result;
    }
}
