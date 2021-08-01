<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Select file
 **/
class SelectFile extends \XLite\View\Button\AButton
{
    /**
     * Name of object to link uploaded file (e.g. equal to 'product', 'category')
     */
    const PARAM_OBJECT = 'object';

    /**
     * Identificator of linked object.
     */
    const PARAM_OBJECT_ID = 'objectId';

    /**
     * Name of the uploaded file object (e.g. 'image', 'icon', 'file')
     */
    const PARAM_FILE_OBJECT = 'fileObject';

    /**
     * Identificator of the uploaded file object. Used if file must be substituted (update action)
     */
    const PARAM_FILE_OBJECT_ID = 'fileObjectId';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_OBJECT         => new \XLite\Model\WidgetParam\TypeString('Object', 'product'),
            static::PARAM_OBJECT_ID      => new \XLite\Model\WidgetParam\TypeInt('Object ID', 0),
            static::PARAM_FILE_OBJECT    => new \XLite\Model\WidgetParam\TypeString('File object', 'image'),
            static::PARAM_FILE_OBJECT_ID => new \XLite\Model\WidgetParam\TypeInt('File object ID', 0),
        ];
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/select_file.js';

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
        $list[] = 'button/css/select_file.css';

        return $list;
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'File upload';
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' select-file-button always-reload';
    }

    /**
     * Return form target
     *
     * @return string
     */
    protected function getTarget()
    {
        return 'select_file';
    }

    /**
     * Return form action
     *
     * @return string
     */
    protected function getAction()
    {
        return 'select';
    }

    /**
     * Get commented data
     *
     * @return array
     */
    protected function getCommentedData()
    {
        return parent::getCommentedData() + [
            'target'                     => $this->getTarget(),
            'action'                     => $this->getAction(),
            static::PARAM_OBJECT         => $this->getParam(static::PARAM_OBJECT),
            static::PARAM_OBJECT_ID      => $this->getParam(static::PARAM_OBJECT_ID),
            static::PARAM_FILE_OBJECT    => $this->getParam(static::PARAM_FILE_OBJECT),
            static::PARAM_FILE_OBJECT_ID => $this->getParam(static::PARAM_FILE_OBJECT_ID),
            'name'                       => $this->getName()
        ];
    }
}