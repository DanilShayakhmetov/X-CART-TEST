<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\AttributeValue\Customer;

/**
 * Attribute value (Textarea)
 */
class Text extends \XLite\View\Product\AttributeValue\Customer\ACustomer
{
    /**
     * Get dir
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/text';
    }

    /**
     * Get attribute type
     *
     * @return string
     */
    protected function getAttributeType()
    {
        return \XLite\Model\Attribute::TYPE_TEXT;
    }

    /**
     * Returns input-specific attributes
     * 
     * @return array
     */
    protected function getInputAttributes()
    {
        return array_merge(
            parent::getInputAttributes(),
            [
                'debounce' => '300'
            ]
        );
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
     * Get actual value
     *
     * @return string
     */
    protected function getValue()
    {
        $result = null;

        $idsOrValues = $this->getSelectedIds();

        if (isset($idsOrValues[$this->getAttribute()->getId()])) {
            return $idsOrValues[$this->getAttribute()->getId()];
        } elseif ($this->getOrderItem()) {
            foreach ($this->getOrderItem()->getAttributeValues() as $av) {
                if ($av->getAttributeId() == $this->getAttribute()->getId()) {
                    $result = $av->getValue();
                }
            }

        } else {
            $result = $this->getAttrValue()->getValue();
        }

        return $result;
    }
}
