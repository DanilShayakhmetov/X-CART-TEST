<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\FormField\Inline\Input\Text;

/**
 * Amount
 */
class Amount extends \XLite\Module\XC\ProductVariants\View\FormField\Inline\Input\Text\DefaultValue
{
    /**
     * Save value
     *
     * @return void
     */
    public function saveValue()
    {
        $qty = $this->getSingleFieldAsWidget()->getValue();
        if ($this->getFields()['qty_origin']['widget']->getValue() !== $qty) {
            parent::saveValue();
        }
    }

    /**
     * @param array $field Field
     *
     * @return mixed
     */
    protected function getFieldEntityValue(array $field)
    {
        if ($field[static::FIELD_NAME] === 'qty_origin') {
            if ($this->getEntity()->getDefaultAmount()) {
                return null;
            }

            return $this->getEntityValue();
        }

        return parent::getFieldEntityValue($field);
    }

    /**
     * Get initial field parameters
     *
     * @param array $field Field data
     *
     * @return array
     */
    protected function getFieldParams(array $field)
    {
        $params = parent::getFieldParams($field);
        $params['value'] = $this->getFieldEntityValue($field);

        return $params;
    }

    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\Module\XC\ProductVariants\View\FormField\Input\Text\Integer';
    }

    /**
     * Define fields
     *
     * @return array
     */
    protected function defineFields()
    {
        $fields = parent::defineFields();

        $fields['qty_origin'] = [
            'name'  => 'qty_origin',
            'class' => 'XLite\View\FormField\Input\Hidden',
        ];

        return $fields;
    }

    /**
     * @param array $field
     *
     * @return array
     */
    protected function validateAmount(array $field)
    {
        $result      = [true, null];
        $variantId   = $this->getEntity() ? $this->getEntity()->getId() : null;
        $originValue = $variantId ? $this->getFields()['qty_origin']['widget']->getValue() : null;
        $value       = $this->getSingleFieldAsWidget()->getValue();

        try {
            $validator = new \XLite\Module\XC\ProductVariants\Core\Validator\VariantQty($variantId, $value, $originValue);
            $validator->validate($field['widget']->getValue());
        } catch (\Exception $e) {
            $result = [
                false,
                $e->getMessage()
            ];
        }

        return $result;
    }
}
