<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\FormField\Input;


use XLite\View\FormField\Select\AttributeValues;

/**
 * Attributes
 */
class Attributes extends \XLite\View\FormField\Input\AInput
{
    const PARAM_VARIANT_ATTRIBUTES = 'variant_attributes';
    const PARAM_PRODUCT = 'product';

    /**
     * @inheritdoc
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_VARIANT_ATTRIBUTES => new \XLite\Model\WidgetParam\TypeCollection('Variant attributes', []),
            static::PARAM_PRODUCT            => new \XLite\Model\WidgetParam\TypeObject('Product', []),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getWrapperClass()
    {
        return parent::getWrapperClass() . ' input-variant-attributes';
    }

    /**
     * @inheritdoc
     */
    public function getFieldType()
    {
        return static::FIELD_TYPE_COMPLEX;
    }

    /**
     * @inheritdoc
     */
    protected function getDir()
    {
        return 'modules/XC/ProductVariants/field/';
    }

    /**
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(static::PARAM_PRODUCT);
    }

    /**
     * @return array
     */
    protected function getVariantAttributes()
    {
        return $this->getParam(static::PARAM_VARIANT_ATTRIBUTES);
    }

    /**
     * @return array
     */
    protected function getAttributesWidgetsContent()
    {
        $result = [];

        foreach ($this->getVariantAttributes() as $attribute) {
            $widget = $this->getChildWidget(
                'XLite\View\FormField\Select\AttributeValues',
                [
                    static::PARAM_FIELD_ONLY         => true,
                    static::PARAM_VALUE              => null,
                    static::PARAM_NAME               => $this->getName() . "[{$attribute->getId()}]",
                    AttributeValues::PARAM_ATTRIBUTE => $attribute,
                    AttributeValues::PARAM_PRODUCT   => $this->getProduct(),
                ]
            );

            $result[] = $widget->getContent();
        }

        return $result;
    }
}