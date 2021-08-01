<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Module\XC\ProductVariants\View\Product\Details\Customer;

/**
 * PhotoBox
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
class PhotoBox extends \XLite\View\Product\Details\Customer\PhotoBox implements \XLite\Base\IDecorator
{
    /**
     * Check - loupe icon is visible or not
     *
     * @return boolean
     */
    protected function isLoupeVisible()
    {
        $result = parent::isLoupeVisible();

        if (!$result && ($product = $this->getProduct()) && $product->hasVariants()) {
            $repo = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductVariants\Model\Image\ProductVariant\Image');

            return $repo->countByProduct($product);
        }

        return $result;
    }
}
