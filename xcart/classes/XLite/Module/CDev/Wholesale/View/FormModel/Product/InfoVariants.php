<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View\FormModel\Product;

use XLite\Core\Database;

/**
 * Decorator Info
 *
 * @Decorator\Depend("XC\ProductVariants")
 */
class InfoVariants extends \XLite\View\FormModel\Product\Info implements \XLite\Base\IDecorator
{
    /**
     * @return string
     */
    protected function getPriceDescriptionTemplate()
    {
        /** @var \XLite\Module\CDev\Wholesale\Model\Product $product */
        $product = $this->getProductEntity();

        if (
            $product
            && $product->hasVariants()
            && $product->isWholesalePricesEnabled()
            && count($product->getWholesalePrices()) > 0
        ) {
            return 'modules/CDev/Wholesale/form_model/product/info/wholesale_variants_defined_link.twig';
        } elseif (
            $product
            && $product->isWholesalePricesEnabled()
            && count($product->getWholesalePrices()) > 0
        ) {
            return 'modules/CDev/Wholesale/form_model/product/info/wholesale_defined_link.twig';
        }

        return parent::getPriceDescriptionTemplate();
    }
}
