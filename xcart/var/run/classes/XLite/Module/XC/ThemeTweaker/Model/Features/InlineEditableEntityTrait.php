<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Model\Features;

use XLite\Module\XC\ThemeTweaker\Core\ThemeTweaker;

trait InlineEditableEntityTrait
{
    /**
     * @return array
     */
    abstract public function defineEditableProperties();

    /**
     * @param string $property Entity property name
     * @return array
     */
    abstract public function getFieldMetadata($property);

    /**
     * Checks if given property is available to modification through layout editor mode.
     *
     * @param  string  $property Checked entity property
     * @return boolean
     */
    public function isEditableProperty($property)
    {
        $editable = $this->defineEditableProperties();

        return in_array($property, $editable, true);
    }

    /**
     * Provides metadata for the inline editable property feature
     * @return array
     */
    public function getInlineEditableMetadata()
    {
        return ThemeTweaker::getInstance()->isInInlineEditorMode()
            ? [
                'data-inline-editable' => 'data-inline-editable',
                'data-inline-editor-config' => json_encode($this->getInlineEditorConfig()),
            ]
            : [];
    }

    /**
     * This is config for layout editor mode editor.
     *
     * @return array
     */
    public function getInlineEditorConfig()
    {
        $config = [
            'toolbarInline'                  => true,
            'toolbarVisibleWithoutSelection' => true,
            'charCounterCount'               => false,
            'videoUploadURL'                 => $this->getVideoUploadURL(),
            'videoAllowedTypes'              => ['webm', 'jpg', 'ogg', 'mp4', 'avi'],
            'videoMaxSize'                   => \XLite\Core\Converter::getUploadFileMaxSize(),
            'imageMaxSize'                   => \XLite\Core\Converter::getUploadFileMaxSize(),
            'imageUploadURL'                 => $this->getImageUploadURL(),
            'imageManagerLoadURL'            => $this->getImageManagerLoadURL(),
            'imageManagerDeleteURL'          => $this->getImageManagerDeleteURL(),
            'imageUploadParam'               => 'file',
            'imageUploadParams'              => [
                'url_param_name' => 'link',
            ],
            'zIndex'                         => 9990,
            'requestHeaders'                 => [
                'X-Requested-With' => 'XMLHttpRequest',
            ],
            'toolbarButtons'                 => $this->getToolbarButtons(),
            'toolbarButtonsMD'               => $this->getToolbarButtons(),
            'toolbarButtonsSM'               => $this->getToolbarButtons(),
            'toolbarButtonsXS'               => $this->getToolbarButtons(),
        ];

        if($this->useCustomColors() && $this->getCustomColors()) {
            $config['colorsBackground'] = $this->getCustomColors();
            $config['colorsText'] = $this->getCustomColors();
            $config['colorsStep'] = 6;
        }

        return $config;
    }

    /**
     * @return array
     */
    protected function getToolbarButtons()
    {
        return [
            'fontFamily', 'fontSize',
            '|', 'bold', 'italic', 'underline', 'strikeThrough', 'color',
            '-', 'paragraphFormat', 'paragraphStyle', 'align', 'formatOL', 'formatUL',
            '|', 'indent', 'outdent',
            '-', 'insertImage', 'insertTable', 'insertLink', 'insertVideo',
            '|', 'undo', 'redo', 'html'
        ];
    }

    /**
     * @return string
     */
    protected function getImageUploadURL()
    {
        $params = [
            'mode'  => 'json',
            'type'  => 'image',
        ];

        return \XLite\Core\Converter::buildFullURL('files', 'upload_from_file', $params, \XLite::getAdminScript());
    }

    /**
     * @return string
     */
    protected function getVideoUploadURL()
    {
        $params = [
            'mode'  => 'json',
            'type'  => 'video',
        ];

        return \XLite\Core\Converter::buildFullURL('files', 'upload_from_file', $params, \XLite::getAdminScript());
    }

    /**
     * @return string
     */
    protected function getImageManagerLoadURL()
    {
        return \XLite\Core\Converter::buildFullURL('files', 'get_image_manager_list', [], \XLite::getAdminScript());
    }

    /**
     * @return string
     */
    protected function getImageManagerDeleteURL()
    {
        return \XLite\Core\Converter::buildFullURL('files', 'remove_from_image_manager', [], \XLite::getAdminScript());
    }

    /**
     * @return bool
     */
    protected function useCustomColors()
    {
        return isset(\XLite\Core\Config::getInstance()->XC->FroalaEditor->use_custom_colors)
            ? (bool) \XLite\Core\Config::getInstance()->XC->FroalaEditor->use_custom_colors
            : false;
    }

    /**
     * @return array
     */
    protected function getCustomColors()
    {
        if (!isset(\XLite\Core\Config::getInstance()->XC->FroalaEditor->custom_colors)) {
            return [];
        }

        $customColors = [];

        $colorsSetting = \XLite\Core\Config::getInstance()->XC->FroalaEditor->custom_colors;
        if ($colorsSetting) {
            $customColors = explode(',', $colorsSetting);

            $customColors = array_map(function($color) {
                return '#' . $color;
            }, $customColors);
        }

        $customColors[] = 'REMOVE';

        return $customColors;
    }
}
