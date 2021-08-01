<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\AttributeValue\Admin;

/**
 * Attribute value (Hidden)
 */
class Hidden extends \XLite\View\Product\AttributeValue\Admin\AAdmin
{
    /**
     * Get dir
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/hidden';
    }

    /**
     * Get attribute type
     *
     * @return string
     */
    protected function getAttributeType()
    {
        return \XLite\Model\Attribute::TYPE_HIDDEN;
    }

    /**
     * Return name of widget class
     *
     * @return string
     */
    protected function getWidgetClass()
    {
        return $this->getAttribute() && !$this->getAttribute()->getProduct()
            ? '\XLite\View\FormField\Input\Text\AttributeOption'
            : '\XLite\View\FormField\Input\Text';
    }

    /**
     * Return field value
     *
     * @param \XLite\Model\AttributeValue\AttributeValueHidden $attributeValue Attribute value
     *
     * @return mixed
     */
    protected function getFieldValue($attributeValue)
    {
        if (is_array($attributeValue)) {
            $attributeValue = reset($attributeValue);
        }

        return $attributeValue && $attributeValue->getAttributeOption()
            ? $attributeValue->getAttributeOption()->getName()
            : '';
    }
}
