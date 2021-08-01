<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\Product\Details\Customer;

use XLite\Core\Cache\ExecuteCached;

/**
 * Product widget
 */
abstract class Widget extends \XLite\View\Product\Details\Customer\Widget implements \XLite\Base\IDecorator
{
    /**
     * Product variant
     *
     * @var mixed
     */
    protected $productVariant;

    /**
     * Return product variant
     *
     * @return boolean|\XLite\Module\XC\ProductVariants\Model\ProductVariant
     */
    protected function getProductVariant()
    {
        if (!isset($this->productVariant)) {
            if ($this->getProduct()->mustHaveVariants()) {
                $this->productVariant = $this->getProduct()->getVariant($this->getAttributeValues());
            }

            if (!$this->productVariant) {
                $this->productVariant = false;
            }
        }

        return $this->productVariant;
    }

    /**
     * Check - 'out of stock' label is visible or not
     *
     * @return boolean
     */
    protected function isOutOfStock()
    {
        return $this->getProductVariant()
            ? $this->getProductVariant()->isOutOfStock()
            : ($this->getProduct()->mustHaveVariants() ? $this->allOptionsSelected() : parent::isOutOfStock());
    }

    /**
     * Alias: is product in stock or not
     *
     * @return boolean
     */
    public function isAllStockInCart()
    {
        return $this->getProductVariant()
            ? $this->getProductVariant()->isAllStockInCart()
            : parent::isAllStockInCart();
    }

    /**
     * @return boolean
     */
    private function allOptionsSelected()
    {
        $attributesData = \XLite\Core\Request::getInstance()->attribute_values;
        $isShowPlaceholderOption = $this->showPlaceholderOption();

        return ExecuteCached::executeCached(
            static function () use ($attributesData, $isShowPlaceholderOption) {
                $attributes = explode(',', $attributesData);

                foreach ($attributes as $attribute) {
                    if ($attribute) {
                        [$id, $value] = explode('_', $attribute);
                        if ($value === 'null' || $value === '') {
                            return false;
                        }
                    }
                }

                return !$isShowPlaceholderOption;
            },
            ['allOptionsSelected', $attributesData, $isShowPlaceholderOption]
        );
    }

    /**
     * @return boolean
     */
    public function showPlaceholderOption()
    {
        if (\XLite\Core\Config::getInstance()->General->force_choose_product_options === 'quicklook') {

            return \XLite::getController()->getTarget() !== 'product';

        } elseif (\XLite\Core\Config::getInstance()->General->force_choose_product_options === 'product_page') {

            return true;
        }

        return false;
    }

    /**
     * Check product availability for sale
     *
     * @return boolean
     */
    protected function isProductAvailableForSale()
    {
        return $this->getProductVariant()
            ? $this->getProductVariant()->isAvailable()
            : ($this->getProduct()->mustHaveVariants() ? !$this->allOptionsSelected() : parent::isProductAvailableForSale());
    }
}
