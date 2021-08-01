<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Product widget
 */
abstract class Widget extends \XLite\View\Product\AProduct
{
    use ExecuteCachedTrait;

    /**
     * Widget parameters
     */
    const PARAM_PRODUCT          = 'product';
    const PARAM_PRODUCT_ID       = 'product_id';
    const PARAM_ATTRIBUTE_VALUES = 'attribute_values';

    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    abstract public function getFingerprint();

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_PRODUCT          => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, 'XLite\Model\Product'),
            static::PARAM_ATTRIBUTE_VALUES => new \XLite\Model\WidgetParam\TypeString('Attribute values IDs', $this->getDefaultAttributeValues()),
        ];
    }

    /**
     * @inheritdoc
     */
    public function setWidgetParams(array $params)
    {
        if (empty($params[static::PARAM_PRODUCT]) && (
                !$this->getParam(static::PARAM_PRODUCT) || isset($params[static::PARAM_PRODUCT_ID])
            )
        ) {
            $params[static::PARAM_PRODUCT] = \XLite\Core\Database::getRepo('XLite\Model\Product')->find(
                isset($params[static::PARAM_PRODUCT_ID])
                    ? $params[static::PARAM_PRODUCT_ID]
                    : $this->getDefaultProductId()
            );
        }

        parent::setWidgetParams($params);
    }

    /**
     * @return integer
     */
    protected function getDefaultProductId()
    {
        return isset(\XLite\Core\Request::getInstance()->product_id)
            ? \XLite\Core\Request::getInstance()->product_id
            : 0;
    }

    /**
     * @return string
     */
    protected function getDefaultAttributeValues()
    {
        return isset(\XLite\Core\Request::getInstance()->attribute_values)
            ? \XLite\Core\Request::getInstance()->attribute_values
            : '';
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        $productId = $this->getParam(self::PARAM_PRODUCT)->getProductId();

        $product = $this->getRuntimeCache(['getProduct', $productId]);
        if (!$product) {
            $product = $this->executeCachedRuntime(function () {
                $product = $this->getParam(self::PARAM_PRODUCT);

                return $product;
            }, ['getProduct', $productId]);

            $product->setAttrValues($this->getAttributeValues());
        }

        return $product;
    }

    /**
     * Return product attributes array from the request parameters
     *
     * @return array
     */
    protected function getAttributeValues()
    {
        $attributeValuesParam = $this->getParam(static::PARAM_ATTRIBUTE_VALUES);

        return $this->executeCachedRuntime(function () use ($attributeValuesParam) {
            $result = [];

            if (is_array($attributeValuesParam)) {
                $result = $attributeValuesParam;
            } else {
                $attributeValues = trim($attributeValuesParam, ',');

                if ($attributeValues) {
                    $attributeValues = explode(',', $attributeValues);
                    foreach ($attributeValues as $attributeValue) {
                        list($attributeId, $valueId) = explode('_', $attributeValue);

                        $result[$attributeId] = $valueId;
                    }
                }
            }

            return $this->getProduct()->prepareAttributeValues($result);
        }, ['getAttributeValues', $attributeValuesParam]);
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getProduct();
    }

    /**
     * Alias: is product in stock or not
     *
     * @return boolean
     */
    public function isAllStockInCart()
    {
        return $this->getProduct()->isAllStockInCart();
    }
}
