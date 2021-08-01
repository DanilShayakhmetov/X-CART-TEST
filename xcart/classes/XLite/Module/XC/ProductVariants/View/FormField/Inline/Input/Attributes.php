<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\FormField\Inline\Input;


class Attributes extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return '\XLite\Module\XC\ProductVariants\View\FormField\Input\Attributes';
    }

    /**
     * Get entity value
     *
     * @return mixed
     */
    protected function getEntityValue()
    {
        return null;
    }

    /**
     * Save value
     *
     * @return void
     */
    public function saveValue()
    {
        $variant = $this->getEntity();
        $values = $this->getSingleFieldAsWidget()->getValue();

        if (is_array($values)) {
            $attributesRepo = \XLite\Core\Database::getRepo('XLite\Model\Attribute');
            foreach ($values as $id => $value) {
                if ($attribute = $attributesRepo->find($id)) {
                    $attributeValue = \XLite\Core\Database::getRepo(
                        $attribute->getAttributeValueClass(
                            $attribute->getType()
                        )
                    )->find($value);

                    if ($attributeValue) {
                        $method = 'addAttributeValue' . $attribute->getType();
                        $variant->$method($attributeValue);
                        $attributeValue->addVariants($variant);
                    }
                }
            }
        }
    }
}