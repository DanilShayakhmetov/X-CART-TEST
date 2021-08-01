<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\FormField\Inline\Input\Text\Product;

/**
 * Price
 */
class Price extends \XLite\View\FormField\Inline\Input\Text\Price
{
    use \XLite\Core\Cache\ExecuteCachedTrait;

    /**
     * Return min clear price of product
     *
     * @return float
     */
    protected function getMinClearPrice()
    {
        return $this->executeCachedRuntime(function() {
            $price = $this->getEntity()->getClearPrice();

            foreach ($this->getEntity()->getVariants() as $variant) {
                if ($variant->getClearPrice() < $price) {
                    $price = $variant->getClearPrice();
                }
            }

            return $price;
        });
    }

    /**
     * Return max clear price of product
     *
     * @return float
     */
    protected function getMaxClearPrice()
    {
        return $this->executeCachedRuntime(function () {
            $price = $this->getEntity()->getClearPrice();

            foreach ($this->getEntity()->getVariants() as $variant) {
                if ($variant->getClearPrice() > $price) {
                    $price = $variant->getClearPrice();
                }
            }

            return $price;
        });
    }

    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        if ($this->isDisplayPriceAsRange()) {
            return 'XLite\Module\XC\ProductVariants\View\FormField\PriceRangeLink';
        }

        return parent::defineFieldClass();
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        $containerClass = parent::getContainerClass();

        if ($this->isDisplayPriceAsRange()) {
            $containerClass = '';
        }

        return $containerClass;
    }

    /**
     * Get initial field parameters
     *
     * @param array $field Field data
     *
     * @return array
     */
    protected function getFieldParams(array $field)
    {
        $params = parent::getFieldParams($field);

        if ($this->isDisplayPriceAsRange()) {
            $params['min_price'] = $this->getMinClearPrice();
            $params['max_price'] = $this->getMaxClearPrice();
            $params['href'] = $this->buildURL('product', '', [
                'product_id' => $this->getEntity()->getProductId(),
                'page' => 'variants',
            ]);
        }

        return $params;
    }

    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function hasSeparateView()
    {
        if ($this->isDisplayPriceAsRange()) {
            return false;
        }

        return parent::hasSeparateView();
    }

    /**
     * Check if product price in list should be displayed as range
     *
     * @return bool
     */
    protected function isDisplayPriceAsRange()
    {
        return ($this->getEntity() ? $this->getEntity()->isDisplayPriceAsRange() : false)
            && (
                $this->getMaxClearPrice() != $this->getMinClearPrice()
                || $this->getEntity()->getPrice() != $this->getMaxClearPrice()
            );
    }
}
