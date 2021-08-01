<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\FormField\Input\Text;

/**
 * FloatInput
 */
class FloatInput extends \XLite\View\FormField\Input\Text\FloatInput
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/ProductVariants/form_field/input/text/float.js';

        return $list;
    }

    /**
     * Sanitize value
     *
     * @return mixed
     */
    protected function sanitizeFloat($value)
    {
        return '' !== $value ? parent::sanitizeFloat($value) : $value;
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $attributes = parent::getCommonAttributes();

        $attributes['value'] = '' !== $attributes['value']
            ? parent::sanitizeFloat($attributes['value'])
            : '';

        return $attributes;
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);
        $classes[] = 'variants-float';

        return $classes;
    }
}
