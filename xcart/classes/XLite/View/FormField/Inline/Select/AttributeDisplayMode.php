<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Select;

/**
 * Display mode of attribute
 */
class AttributeDisplayMode extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return \XLite\View\FormField\Select\AttributeDisplayMode::class;
    }

    /**
     * Get view value
     *
     * @param array $field Field
     *
     * @return string
     */
    protected function getViewValue(array $field)
    {
        $result = '';
        $value = $field['widget']->getValue();

        if ($value) {
            $displayModes = $this->getEntity()::getDisplayModes();
            $result = $displayModes[$value];
        }

        return $result;
    }
}
