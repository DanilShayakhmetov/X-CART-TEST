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
 * @Decorator\Depend({"XC\FreeShipping", "XC\ProductVariants"})
 */
class ProductVariantsFreeShipping extends \XLite\Module\XC\GoogleFeed\Logic\Feed\Step\Products implements \XLite\Base\IDecorator
{
    /**
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $model
     * @return array
     */
    protected function getVariantRecord(\XLite\Module\XC\ProductVariants\Model\ProductVariant $model)
    {
        $result = parent::getVariantRecord($model);

        if ($model->getProduct()->getFreightFixedFee()) {
            $result['g:shipping'] = $this->getShippingRecord($model->getProduct(), $model->getProduct()->getFreightFixedFee());
        }

        return $result;
    }
}