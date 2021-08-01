<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XPay\XPaymentsCloud\View\Product;

/**
 * Product list item widget
 */
 class ListItem extends \XLite\View\Product\ListItemAbstract implements \XLite\Base\IDecorator
{
    /**
     * Cancel shade using 'cancel-ui-state-disabled' class attribute
     *
     * @return object
     */
    public function getProductCellClass()
    {
        $result = parent::getProductCellClass();

        if ($this->getProduct()->isNotAllowedXpaymentsSubscription()) {
            if (!preg_match('/cancel-ui-state-disabled/', $result)) {
                $result .= ' cancel-ui-state-disabled';
            }
        }

        return $this->getSafeValue($result);
    }

    /**
     * Return product labels
     *
     * @return array
     */
    protected function getLabels()
    {
        $labels = parent::getLabels();

        if ($this->getProduct()->isNotAllowedXpaymentsSubscription()) {
            // Add label into the begin of labels list
            $labels = ['subscription' => static::t('Only for registered users')] + $labels;
        }

        return $labels;
    }

    /**
     * Return true if 'Add to cart' buttons shoud be displayed on the list items
     *
     * @return boolean
     */
    protected function isDisplayAdd2CartButton()
    {
        $product = $this->getProduct();

        $result = parent::isDisplayAdd2CartButton();

        if ($result && $product && $product->isNotAllowedXpaymentsSubscription()) {
            // Disable 'Add to cart' button for subscription products
            $result = false;
        }

        return $result;
    }

}
