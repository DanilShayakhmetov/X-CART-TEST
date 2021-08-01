<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\FormField\Inline\Input\Text;

/**
 * Price
 */
class Price extends \XLite\Module\XC\ProductVariants\View\FormField\Inline\Input\Text\DefaultValue
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\Module\XC\ProductVariants\View\FormField\Input\Text\Price';
    }

    /**
     * @inheritdoc
     */
    protected function getPlaceholder()
    {
        return $this->getProduct()
            ? $this->formatPrice($this->getProduct()->getPrice())
            : parent::getPlaceholder();
    }
}
