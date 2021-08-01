<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FileAttachments\View\FormField\Inline\Textarea;

/**
 * Attachment description textarea
 */
class Description extends \XLite\View\FormField\Inline\Textarea\Simple
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/FileAttachments/inline/input/textarea/description.js';

        return $list;
    }

    /**
     * Get empty value
     *
     * @param array $field Field
     *
     * @return string
     */
    protected function getEmptyValue(array $field)
    {
        $label = static::t('File description');
        return "<span class='placeholder'>{$label}</span>";
    }

    /**
     * Return placeholder value
     *
     * @return string
     */
    protected function getPlaceholderValue()
    {
        $fields = $this->getFields();

        return $this->getEmptyValue(array_shift($fields));
    }

    /**
     * Check - escape value or not
     *
     * @return boolean
     */
    protected function isEscapeValue()
    {
        return false;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-textarea-description';
    }

    /**
     * Get container attributes
     *
     * @return array
     */
    protected function getContainerAttributes()
    {
        return ['data-empty' => str_replace('"', '\"', $this->getPlaceholderValue())]
               + parent::getContainerAttributes();
    }
}