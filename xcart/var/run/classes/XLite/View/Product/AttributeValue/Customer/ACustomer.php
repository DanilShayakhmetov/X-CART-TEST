<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\AttributeValue\Customer;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Abstract attribute value (customer)
 */
abstract class ACustomer extends \XLite\View\Product\AttributeValue\AAttributeValue
{
    use ExecuteCachedTrait;

    /**
     * Widget param names
     */
    const PARAM_ORDER_ITEM  = 'orderItem';
    const PARAM_NAME_PREFIX = 'namePrefix';
    const PARAM_NAME_SUFFIX = 'nameSuffix';

    /**
     * Selected attribute value ids
     *
     * @var array
     */
    protected $selectedIds = null;

    /**
     * Return field name
     *
     * @return string
     */
    protected function getName()
    {
        return sprintf(
            '%sattribute_values%s[%d]',
            $this->getParam(static::PARAM_NAME_PREFIX),
            $this->getParam(static::PARAM_NAME_SUFFIX),
            $this->getAttribute()->getId()
        );
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ORDER_ITEM  => new \XLite\Model\WidgetParam\TypeObject(
                'Order item', null, false, 'XLite\Model\OrderItem'
            ),
            static::PARAM_NAME_PREFIX => new \XLite\Model\WidgetParam\TypeString(
                'Field name prefix', '', false
            ),
            static::PARAM_NAME_SUFFIX => new \XLite\Model\WidgetParam\TypeString(
                'Field name suffix', '', false
            ),
        ];
    }

    /**
     * Return field attribute
     *
     * @return \XLite\Model\OrderItem
     */
    protected function getOrderItem()
    {
        return $this->getParam(self::PARAM_ORDER_ITEM);
    }

    /**
     * Return selected attribute values ids or values for text type
     *
     * @return array
     */
    protected function getSelectedIds()
    {
        return $this->executeCachedRuntime(function () {
            return $this->defineSelectedIds();
        }, ['getSelectedIds', $this->getProduct()->getProductId()]);
    }

    /**
     * Get list of selected attribute values as array(<attr ID> => <attr value or value ID>)
     *
     * @return array
     */
    protected function defineSelectedIds()
    {
        $result     = [];

        if (method_exists(\XLite::getController(), 'getSelectedAttributeValuesIds')) {
            /** @see \XLite\Controller\Customer\ChangeAttributeValues */
            $result = \XLite::getController()->getSelectedAttributeValuesIds();
        }

        $attrValues = $this->getProduct()->getAttrValues();

        if (!empty($attrValues)) {
            foreach ($attrValues as $k => $attributeValue) {
                $actualAttributeValue = null;

                if (is_array($attributeValue)
                    && isset($attributeValue['attributeValue'], $attributeValue['value'])
                ) {
                    $actualValue = $attributeValue['value'];
                    $attributeValue = $attributeValue['attributeValue'];
                }

                if ($attributeValue instanceof \XLite\Model\OrderItem\AttributeValue) {
                    $actualAttributeValue = $attributeValue->getAttributeValue();

                } elseif ($attributeValue instanceof \XLite\Model\AttributeValue\AAttributeValue) {
                    $actualAttributeValue = $attributeValue;

                } else {
                    $result[$k] = $attributeValue;
                }

                if ($actualAttributeValue) {
                    if ($actualAttributeValue instanceof \XLite\Model\AttributeValue\AttributeValueText) {
                        if (isset($actualValue)
                            || !isset($result[$actualAttributeValue->getAttribute()->getId()])
                        ) {
                            $result[$actualAttributeValue->getAttribute()->getId()] = isset($actualValue)
                                ? $actualValue
                                : $actualAttributeValue->getValue();
                        }

                    } else {
                        $result[$actualAttributeValue->getAttribute()->getId()] = $actualAttributeValue->getId();
                    }
                }
            }

            ksort($result);

        }

        return $result;
    }

    /**
     * Returns input-specific attributes
     * 
     * @return array
     */
    protected function getInputAttributes()
    {
        return [];
    }

    /**
     * Get dir
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/attribute_value';
    }
}
