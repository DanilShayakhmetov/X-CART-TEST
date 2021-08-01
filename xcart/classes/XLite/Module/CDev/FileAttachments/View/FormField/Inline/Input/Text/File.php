<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FileAttachments\View\FormField\Inline\Input\Text;

/**
 * Text
 */
class File extends \XLite\View\FormField\Inline\Input\Text
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/FileAttachments/inline/input/file.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/FileAttachments/inline/input/file.css';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function getCommonFiles()
    {
        return array_merge_recursive(parent::getCommonFiles(), [
            static::RESOURCE_CSS => ['css/files.css'],
        ]);
    }

    /**
     * Get view template
     *
     * @return string
     */
    protected function getViewTemplate()
    {
        return 'modules/CDev/FileAttachments/inline/input/file.view.twig';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-text-file ' . $this->getFileIconType();
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
        return $this->getEntity()->getStorage()->getFileName();
    }

    /**
     * Get field value from entity
     *
     * @param array $field Field
     *
     * @return mixed
     */
    protected function getFieldEntityValue(array $field)
    {
        return $this->getEntity()->getTitle() ?: $this->getEmptyValue($field);
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
     * Return file size
     *
     * @return string
     */
    protected function getFileSize()
    {
        $result = '';
        $size   = $this->getEntity()->getStorage()->getSize();

        if ($size) {
            $result = \XLite\Core\Converter::convertShortSizeToHumanReadable($size);
        } elseif ($this->getEntity()->getStorage()->isURL()) {
            $result = static::t('Link');
        }

        return $result;
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
     * Returns file icon style
     *
     * @return string
     */
    protected function getFileIconType()
    {
        return 'icon-type-' . $this->getEntity()->getIconType();
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
