<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View\FormField;

use XLite\Model\WidgetParam\TypeString;
use XLite\Module\XC\Onboarding\View\InlineFileUploader;
use XLite\View\FormField\FileUploader\Image;

class InlineImageUploader extends Image
{
    const PARAM_UPLOADED_MESSAGE = 'uploadedMessage';

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_UPLOADED_MESSAGE => new TypeString('Message for uploaded file', ''),
        ];
    }

    /**
     * @return string
     */
    protected function getFileUploaderWidget()
    {
        return InlineFileUploader::class;
    }

    /**
     * @return string
     */
    protected function getFieldTemplate()
    {
        return '../modules/XC/Onboarding/file_uploader/single.twig';
    }
}