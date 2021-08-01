<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Checkbox\Switcher\Attribute;

use \XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Switcher for displayAbove property
 */
class DisplayAboveProperty extends \XLite\View\FormField\Inline\Input\Checkbox\Switcher\OnOff
{
    use ExecuteCachedTrait;

    protected function isForceEnabled()
    {
        return $this->executeCachedRuntime(function () {
            $entity = $this->getEntity();

            foreach ($this->getProduct()->getEditableAttributes() as $attribute) {
                if ($entity->getId() === $attribute->getId()) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Preprocess value before save: return 1 or 0
     *
     * @param mixed $value Value
     *
     * @return array
     */
    protected function preprocessValueBeforeSave($value)
    {
        $value = $this->isForceEnabled() ?: $value;

        return array(
            'displayAbove' => $value,
            'product'      => $this->getProduct(),
        );
    }

    /**
     * Get entity value
     *
     * @return mixed
     */
    protected function getEntityValue()
    {
        return $this->isForceEnabled()
            ?: $this->getEntity()->getDisplayAbove($this->getProduct());
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
        $list = parent::getFieldParams($field);

        if ($this->isForceEnabled()) {
            $list[\XLite\View\FormField\Input\Checkbox\OnOff::PARAM_DISABLED] = true;
        }

        return $list;
    }
}
