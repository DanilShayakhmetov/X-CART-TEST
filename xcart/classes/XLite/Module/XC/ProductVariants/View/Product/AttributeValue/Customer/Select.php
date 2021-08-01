<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\Product\AttributeValue\Customer;

/**
 * Attribute value (Select)
 */
abstract class Select extends \XLite\View\Product\AttributeValue\Customer\Select implements \XLite\Base\IDecorator
{
    /**
     * @return mixed|null
     */
    protected function defineAttributeValue()
    {
        /** @var \XLite\Model\AttributeValue\AttributeValueSelect[] $attributeValue */
        $attributeValue = parent::defineAttributeValue();
        $product = $this->getProduct();
        $result = [];

        if ($product->mustHaveVariants()) {
            $selectedIds = $this->getSelectedIds();
            foreach ($attributeValue as $value) {
                $variantAttributeIds = array_replace(
                    $selectedIds,
                    [$value->getAttribute()->getId() => $value->getId()]
                );
                $isSelectedAlready = isset($selectedIds[$value->getAttribute()->getId()])
                    && $selectedIds[$value->getAttribute()->getId()] === $value->getId()
                    && $this->getAttributeDisplayMode() === \XLite\Model\Attribute::SELECT_BOX_MODE;

                if (!$product->getVariantByAnyAttributeValuesIds($variantAttributeIds) && !$isSelectedAlready) {
                    $value->setVariantAvailable(false);
                }

                $result[] = $value;
            }
        } else {
            $result = $attributeValue;
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getOptionTemplate()
    {
        if ($this->getProduct()->mustHaveVariants()) {
            return 'modules/XC/ProductVariants/product/attribute_value/select/option.twig';
        }

        return parent::getOptionTemplate();
    }
}
