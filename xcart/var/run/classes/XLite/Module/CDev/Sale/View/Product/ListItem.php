<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\CDev\Sale\View\Product;

/**
 * Product list item widget
 */
 class ListItem extends \XLite\Module\XC\CustomerAttachments\View\Product\ListItem implements \XLite\Base\IDecorator
{
    /**
     * Return product labels
     *
     * @return array
     */
    protected function getLabels()
    {
        $product = $this->getProduct();

        $label = \XLite\Module\CDev\Sale\Core\Labels::getLabel($product);

        if ($product->hasParticipateSale() && !$label) {
            $widget = $this->getWidget(
                array(
                    'product'   => $product,
                ),
                'XLite\View\Price'
            );
            $widget->getSalePriceLabel();
        }

        return parent::getLabels() + \XLite\Module\CDev\Sale\Core\Labels::getLabel($product);
    }
}