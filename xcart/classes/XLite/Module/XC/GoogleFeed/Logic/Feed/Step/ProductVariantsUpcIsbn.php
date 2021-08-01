<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\GoogleFeed\Logic\Feed\Step;

/**
 * Products step
 *
 * @Decorator\Depend({"XC\SystemFields", "XC\ProductVariants"})
 */
class ProductVariantsUpcIsbn extends \XLite\Module\XC\GoogleFeed\Logic\Feed\Step\Products implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantMpn(\XLite\Module\XC\ProductVariants\Model\ProductVariant $model)
    {
        return $model->getDisplayMnfVendor() ?: parent::getVariantMpn($model);
    }

    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantGtin(\XLite\Module\XC\ProductVariants\Model\ProductVariant $model)
    {
        return $model->getDisplayUpcIsbn() ?: parent::getVariantGtin($model);
    }
}