<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\FormField\Inline;

/**
 * Abstract order status
 */
abstract class AButton extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function hasSeparateView()
    {
        return false;
    }

    /**
     * @param array $field Field
     * @param mixed $value Value
     *
     * @throws \Exception
     */
    protected function saveFieldEntityValue(array $field, $value)
    {
        $entity = $this->getEntity();

        $configVariable = $entity->getUniqueIdentifier() . '_style_' . $field['field'][static::FIELD_NAME];

        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'category' => 'CDev\Paypal',
            'name'     => $configVariable,
            'value'    => $value,
        ]);
    }
}
