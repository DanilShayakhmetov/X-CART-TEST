<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Product attribute values
 */
class AttributeValues extends \XLite\View\AView
{
    use ExecuteCachedTrait;

    /**
     * Widget param names
     */
    const PARAM_ORDER_ITEM = 'orderItem';
    const PARAM_PRODUCT    = 'product';
    const PARAM_IDX        = 'idx';
    const PARAM_INCLUDE_NON_EDITABLE = 'includeNonEditable';

    /**
     * @var array
     */
    protected $editableAttributeIds = [];

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'product/details/parts/attributes_modify';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getAttributes();
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ORDER_ITEM => new \XLite\Model\WidgetParam\TypeObject(
                'Order item', null, false, '\XLite\Model\OrderItem'
            ),
            self::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject(
                'Product', null, false, '\XLite\Model\Product'
            ),
            self::PARAM_IDX => new \XLite\Model\WidgetParam\TypeInt(
                'Index of order item', 0, false
            ),
            self::PARAM_INCLUDE_NON_EDITABLE => new \XLite\Model\WidgetParam\TypeBool(
                'Include not editable attributes', false
            ),
        );
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        $orderItem = $this->getParam(static::PARAM_ORDER_ITEM);

        return $orderItem
            ? $orderItem->getProduct()
            : ($this->getParam(static::PARAM_PRODUCT) ?: \XLite::getController()->getProduct());
    }

    /**
     * @return boolean
     */
    protected function getIncludeNonEditable()
    {
        return $this->getParam(static::PARAM_INCLUDE_NON_EDITABLE);
    }

    /**
     * Define attributes
     *
     * @return array
     */
    protected function defineAttributes()
    {
        $attributes = [];
        $product = $this->getProduct();
        /** @var \XLite\Model\Attribute $attribute */
        foreach ($product->getEditableAttributes() as $attribute) {
            $attributes[$attribute->getId()] = $attribute;
            $this->editableAttributeIds[$attribute->getId()] =  $attribute->getId();
        }

        if ($this->getIncludeNonEditable()) {
            $visibleAttributes = $product->getVisibleAttributes();
            foreach ($visibleAttributes as $attribute) {
                if ($attribute->getDisplayAbove($product)) {
                    $attributes[$attribute->getId()] = $attribute;
                }
            }

            uasort(
                $attributes,
                function ($a, $b) use ($product) {
                    return $a->getPosition($product) <=> $b->getPosition($product);
                }
            );
        }

        return $attributes;
    }

    /**
     * @param \XLite\Model\Attribute $attribute
     *
     * @return string
     */
    protected function getAttrStringValue(\XLite\Model\Attribute $attribute)
    {
        $value = $attribute->getAttributeValue($this->getProduct(), true);
        if (is_array($value)) {
            $value = implode(\XLite\Model\Attribute::DELIMITER, $value);
        }

        $value = \XLite\Model\Attribute::TYPE_TEXT === $attribute->getType()
            ? $value
            : htmlspecialchars($value);

        return $value;
    }

    /**
     * @param $attribute
     *
     * @return bool
     */
    protected function isEditableAttribute($attribute)
    {
        return isset($this->editableAttributeIds[$attribute->getId()]);
    }

    /**
     * Get attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        return $this->executeCachedRuntime(function () {
            return $this->defineAttributes();
        }, ['getAttributes', $this->getProduct()->getProductId()]);
    }

    /**
     * Get order item index
     *
     * @return integer
     */
    protected function getIdx()
    {
        return $this->getParam(static::PARAM_IDX);
    }

    /**
     * Get order item index
     *
     * @return integer
     */
    protected function getCommonFieldName()
    {
        return 0 < $this->getParam(static::PARAM_IDX)
            ? 'order_items'
            : 'new';
    }
    
    /**
     * Return specific CSS class for attribute wrapper(default <li>)
     *
     * @param $attribute \XLite\Model\Attribute
     *
     * @return string
     */
    protected function getAttributeCSSClass($attribute)
    {
        return '';
    }
}
