<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\AttributeValue;

/**
 * Attribute value (hidden)
 *
 * @Entity
 * @Table  (name="attribute_values_hidden",
 *      indexes={
 *          @Index (name="product_id", columns={"product_id"}),
 *          @Index (name="attribute_id", columns={"attribute_id"}),
 *          @Index (name="attribute_option_id", columns={"attribute_option_id"})
 *      }
 * )
 */
class AttributeValueHidden extends \XLite\Model\AttributeValue\AAttributeValue
{
    /**
     * Attribute option
     *
     * @var \XLite\Model\AttributeOption
     *
     * @ManyToOne  (targetEntity="XLite\Model\AttributeOption")
     * @JoinColumn (name="attribute_option_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attribute_option;

    /**
     * Return attribute value as string
     *
     * @return string
     */
    public function asString()
    {
        /** @see \XLite\Model\AttributeOptionTranslation */
        return $this->getAttributeOption()->getName();
    }

    /**
     * Clone
     *
     * @return static
     */
    public function cloneEntity()
    {
        /** @var static $newEntity */
        $newEntity = parent::cloneEntity();

        if ($this->getAttributeOption()) {
            $attributeOption = $this->getAttributeOption();
            $newEntity->setAttributeOption($attributeOption);
        }

        return $newEntity;
    }

    /**
     * Set attribute_option
     *
     * @param \XLite\Model\AttributeOption $attributeOption
     *
     * @return static
     */
    public function setAttributeOption(\XLite\Model\AttributeOption $attributeOption = null)
    {
        $this->attribute_option = $attributeOption;

        return $this;
    }

    /**
     * Get attribute_option
     *
     * @return \XLite\Model\AttributeOption
     */
    public function getAttributeOption()
    {
        return $this->attribute_option;
    }
}
