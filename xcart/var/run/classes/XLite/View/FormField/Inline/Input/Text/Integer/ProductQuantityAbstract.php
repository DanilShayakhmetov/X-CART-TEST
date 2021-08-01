<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Text\Integer;

/**
 * Product quantity
 */
abstract class ProductQuantityAbstract extends \XLite\View\FormField\Inline\Input\Text\Integer
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
            $this->getEntity()->setAmount($qty);
        }
    }

    /**
     * Get entity value for field
     *
     * @return mixed
     */
    protected function getEntityValue()
    {
        return $this->getEntity()->getPublicAmount();
    }

    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function isEditable()
    {
        return parent::isEditable() && $this->getEntity()->getInventoryEnabled();
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
        $params['min'] = 0;

        return $params;
    }

    /**
     * Get view template
     *
     * @return string
     */
    protected function getViewTemplate()
    {
        return 'form_field/inline/input/text/integer/product_quantity.twig';
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
     * Column label is empty for hidden field
     *
     * @param array $field
     *
     * @return string
     */
    protected function getViewValueQty_origin(array $field)
    {
        return '';
    }

    /**
     * @param array $field
     *
     * @return array
     */
    protected function validateQty(array $field)
    {
        $result      = [true, null];
        $productId   = $this->getEntity() ? $this->getEntity()->getProductId() : null;
        $originValue = $productId ? $this->getFields()['qty_origin']['widget']->getValue() : null;
        $value       = $this->getSingleFieldAsWidget()->getValue();

        try {
            $validator = new \XLite\Core\Validator\ProductQty($productId, $value, $originValue);
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

