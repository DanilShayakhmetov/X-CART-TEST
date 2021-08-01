<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * File Selector Dialog widget
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class FileSelectorDialog extends \XLite\View\SimpleDialog
{
    const MODE_ALL = 'all';
    const MODE_URL = 'url';

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'select_file';

        return $list;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Select file';
    }

    /**
     * Defines the message for uploading files
     *
     * @return string
     */
    protected function getUploadFileMessage()
    {
        $filesize = \XLite\Core\Converter::getUploadFileMaxSize();
        return static::t(
            'The maximum file size that can be uploaded: X',
            array('upload_max_filesize' => \XLite\Core\Converter::convertShortSizeToHumanReadable($filesize))
        );
    }

    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getBody()
    {
        return 'file_selector/body.twig';
    }

    /**
     * Return parameters to use in file dialog form
     *
     * @return array
     */
    protected function getFileDialogParams()
    {
        $modelForm = $this->getModelForm();

        return array(
            // Inner name (inner identification) of object which is joined with file (product, category)
            // It identifies the model to join file with
            'object'       => $modelForm->getObject(),
            // Identificator of object joined with file
            'objectId'     => $modelForm->getObjectId(),
            // Inner name (inner identification) of file (image, attachment)
            // It identifies the model to store file in
            'fileObject'   => $modelForm->getFileObject(),
            // Identificator of file object (zero for NEW file)
            'fileObjectId' => $modelForm->getFileObjectId(),
        );
    }

    /**
     * Return allowed file select modes
     *
     * @return string[]
     */
    protected function getModes()
    {
        return [
            static::MODE_ALL,
            static::MODE_URL,
        ];
    }

    /**
     * Return file select mode
     *
     * @return string
     */
    protected function getMode()
    {
        return in_array(\XLite\Core\Request::getInstance()->select_mode, $this->getModes(), true)
            ? \XLite\Core\Request::getInstance()->select_mode
            : static::MODE_ALL;
    }

    /**
     * Check if select mode is all
     *
     * return boolean
     */
    protected function isAllMode()
    {
        return $this->getMode() === static::MODE_ALL;
    }

    /**
     * Check if select mode is url
     *
     * return boolean
     */
    protected function isUrlMode()
    {
        return $this->getMode() === static::MODE_URL;
    }
}
