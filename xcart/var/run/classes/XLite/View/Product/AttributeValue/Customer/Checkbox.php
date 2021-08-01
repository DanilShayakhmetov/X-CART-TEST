<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\AttributeValue\Customer;

/**
 * Attribute value (Checkbox)
 */
class Checkbox extends \XLite\View\Product\AttributeValue\Customer\ACustomer
{
    /**
     * Get dir
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/checkbox';
    }

    /**
     * Get attribute type
     *
     * @return string
     */
    protected function getAttributeType()
    {
        return \XLite\Model\Attribute::TYPE_CHECKBOX;
    }

    /**
     * Return values
     * @bc: remove in 5.4
     *
     * @return array
     */
    protected function getAttrValues()
    {
        return $this->getAttributeValue();
    }

    /**
     * Returns input-specific attributes
     * 
     * @return array
     */
    protected function getInputAttributes()
    {
        $attrValues = $this->getAttrValues();

        $result = [];

        foreach ($attrValues[1]::getModifiers() as $field => $options) {
            $modifier = $this->getAbsoluteModifierValue($attrValues[1], $field);
            if (0 !== $modifier) {
                $result['data-modifier-' . $field] = $modifier;
            }
        }

        if ($this->isCheckedValue()) {
            $result['data-init-selected'] = 1;
        }

        return array_merge(
            parent::getInputAttributes(),
            $result
        );
    }
    
    /**
     * @return mixed|null
     */
    protected function defineAttributeValue()
    {
        $result = [];

        /** @var \XLite\Model\AttributeValue\AttributeValueCheckbox[] $attributeValue */
        $attributeValue = parent::defineAttributeValue();
        foreach ($attributeValue as $value) {
            $result[(int) $value->getValue()] = $value;
        }

        ksort($result);

        return $result;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getTitle()
    {
        return $this->getAttribute()->getName();
    }

    /**
     * Return modifier title
     *
     * @return string
     */
    protected function getModifierTitle()
    {
        $modifiers = array();
        foreach ($this->getAttrValues() as $k => $value) {
            foreach ($value::getModifiers() as $field => $v) {
                if (!isset($modifiers[$field])) {
                    $modifiers[$field] = 0;
                }
                $modifiers[$field] += (-1 + 2 * $k) * $this->getAbsoluteModifierValue($value, $field);
            }
        }

        foreach ($modifiers as $field => $modifier) {
            if (0 == $modifier) {
                unset($modifiers[$field]);

            } else {
                $modifiers[$field] = \XLite\Model\AttributeValue\AttributeValueSelect::formatModifier($modifier, $field);
            }
        }

        return $modifiers
            ? ' <span>' . implode(', ', $modifiers) . '</span>'
            : '';
    }

    /**
     * @param \XLite\Model\AttributeValue\Multiple $value
     * @param $field
     * @return float
     */
    protected function getAbsoluteModifierValue(\XLite\Model\AttributeValue\Multiple $value, $field)
    {
        return $value->getAbsoluteValue($field);
    }

    /**
     * Return value is checked or not flag
     *
     * @return boolean
     */
    protected function isCheckedValue()
    {
        $res = false;

        $selectedIds = $this->getSelectedIds();
        $values = $this->getAttrValues();

        if (0 < count($values)) {
            if (0 < count($selectedIds)) {
                foreach ($values as $k => $v) {
                    $res = isset($selectedIds[$v->getAttribute()->getId()])
                        ? $selectedIds[$v->getAttribute()->getId()] == $v->getId() && $v->getValue()
                        : $res;

                    if ($res) {
                        break;
                    }
                }

            } else {
                foreach ($values as $k => $v) {
                    $res = $v->getValue() && $v->getDefaultValue();

                    if ($res) {
                        break;
                    }
                }
            }
        }

        return $res;
    }
}
