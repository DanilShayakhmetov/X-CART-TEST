<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Text\Position;

/**
 * Order by position (soratable)
 */
class Move extends \XLite\View\FormField\Inline\Input\Text\Position
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'form_field/inline/input/text/position/move.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'form_field/inline/input/text/position/move.js';

        return $list;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return trim(str_replace('inline-position', '', parent::getContainerClass()) . ' inline-move');
    }

    /**
     * Get field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'form_field/inline/input/text/position/move.twig';
    }

    /**
     * Check - field has view or not
     *
     * @return boolean
     */
    protected function hasSeparateView()
    {
        return false;
    }

    /**
     * Save field value
     *
     * @param array $field Field
     *
     * @return void
     */
    protected function saveFieldValue(array $field)
    {
        $value = $field['widget']->getValue();
        $value = $this->preprocessValueBeforeSave($value);

        $method = 'preprocessValueBeforeSave' . ucfirst($field['field'][static::FIELD_NAME]);
        if (method_exists($this, $method)) {
            // $method assemble from 'preprocessValueBeforeSave' + field name
            $value = $this->$method($value);
        }

        $this->saveFieldEntityValue($field, $value);
    }

}
