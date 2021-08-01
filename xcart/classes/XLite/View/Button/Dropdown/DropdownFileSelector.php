<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Dropdown;

/**
 * File selector
 */
class DropdownFileSelector extends \XLite\View\Button\Dropdown\ADropdown
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

        $this->widgetParams += array(
            static::PARAM_OBJECT         => new \XLite\Model\WidgetParam\TypeString('Object', 'product'),
            static::PARAM_OBJECT_ID      => new \XLite\Model\WidgetParam\TypeInt('Object ID', 0),
            static::PARAM_FILE_OBJECT    => new \XLite\Model\WidgetParam\TypeString('File object', 'image'),
            static::PARAM_FILE_OBJECT_ID => new \XLite\Model\WidgetParam\TypeInt('File object ID', 0),
        );
    }

    /**
     * Return CSS files list
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'button/css/dropdown-file-selector.css';

        return $list;
    }

    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $buttons = [
            'local'  => [
                'class'    => '\XLite\View\Button\SelectFile',
                'params'   => [
                    'label'      => 'from local computer',
                    'style'      => 'action link list-action',
                    'icon-style' => 'fa fa-television',
                    'buttonName' => 'uploaded_file'
                ],
                'position' => 100,
            ],
            'server' => [
                'class'    => 'XLite\View\Button\BrowseServerInstant',
                'params'   => [
                    'label'      => 'from local server',
                    'style'      => 'action link list-action',
                    'icon-style' => 'fa fa-server-2block',
                    'buttonName' => 'local_server_file'
                ],
                'position' => 200,
            ],
            'url'    => [
                'class'    => 'XLite\View\Button\SelectFileURL',
                'params'   => [
                    'label'      => 'Via URL',
                    'style'      => 'action link list-action',
                    'icon-style' => 'fa fa-chain-another',
                    'buttonName' => 'url'
                ],
                'position' => 300,
            ],
        ];

        $formParams = [
            static::PARAM_OBJECT         => $this->getParam(static::PARAM_OBJECT),
            static::PARAM_OBJECT_ID      => $this->getParam(static::PARAM_OBJECT_ID),
            static::PARAM_FILE_OBJECT    => $this->getParam(static::PARAM_FILE_OBJECT),
            static::PARAM_FILE_OBJECT_ID => $this->getParam(static::PARAM_FILE_OBJECT_ID),
        ];

        foreach ($buttons as &$button) {
            $button['params'] += $formParams;
        }

        return $buttons;
    }

    /**
     * @return boolean
     */
    protected function getUseCaretButton()
    {
        return false;
    }

    /**
     * Get style
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' dropdown-file-selector';
    }
}