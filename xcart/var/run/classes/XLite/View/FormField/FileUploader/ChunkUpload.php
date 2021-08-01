<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\FileUploader;


class ChunkUpload extends \XLite\View\FormField\AFormField
{
    const PARAM_SUCCESS_ACTION = 'successAction';
    const PARAM_EXTENSIONS = 'extensions';

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/js/chunk_upload.js';

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/css/chunk_upload.css';

        return $list;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_SUCCESS_ACTION => new \XLite\Model\WidgetParam\TypeString('Id', ''),
            self::PARAM_EXTENSIONS => new \XLite\Model\WidgetParam\TypeString('Extensions, accept attribute', ''),
        ];
    }

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_FILE;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'file_uploader/chunk_upload.twig';
    }

    /**
     * Return addition action on success
     *
     * @return string
     */
    protected function getSuccessAction()
    {
        return $this->getParam(self::PARAM_SUCCESS_ACTION);
    }

    /**
     * Return addition action on success
     *
     * @return string
     */
    protected function getExtensions()
    {
        return $this->getParam(self::PARAM_EXTENSIONS);
    }

    /**
     * Register some data that will be sent to template as special HTML comment
     *
     * @return array
     */
    protected function getCommentedData()
    {
        $chunk_size = min(
            round(\XLite\Core\Converter::convertShortSize(ini_get('post_max_size')) * 0.9),
            \XLite\Core\Converter::convertShortSize('2M')
        );

        $result = [
            'chunk_size'  => $chunk_size,
            'form_params' => [
                'target' => 'chunk_upload',
                'action' => 'process_chunk',
            ],
            'success_form_params' => [
                'target' => 'chunk_upload',
                'action' => 'success',
            ],
        ];

        if ($this->getSuccessAction()) {
            $result['success_form_params']['success_action'] = $this->getSuccessAction();
        }

        return $result;
    }
}