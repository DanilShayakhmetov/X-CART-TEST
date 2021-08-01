<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomerAttachments\View\FormField\Input\Text;

use XLite\Core\Converter;

/**
 * Field for file size
 */
class FileSizeInteger extends \XLite\View\FormField\Input\Text\FloatInput
{
    /**
     * Widget param
     */
    const PARAM_VALUE_E = 4;

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[static::PARAM_COMMENT]->setValue($this->getFileSizeComment());
        $this->widgetParams[static::PARAM_E]->setValue(static::PARAM_VALUE_E);

        $maxSizeLimit = round(Converter::getUploadFileMaxSize() / Converter::MEGABYTE, 3);
        $this->widgetParams[static::PARAM_MAX]->setValue(
            round($maxSizeLimit, $this->getE())
        );
    }

    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        $params[static::PARAM_HELP] = $this->getFileSizeHelp();
        parent::setWidgetParams($params);
    }

    /**
     * Get human readable file size comment
     *
     * @return string
     */
    protected function getFileSizeComment()
    {
        $fileSizeLimitInMB = round(Converter::getUploadFileMaxSize() / Converter::MEGABYTE, 3);

        return \XLite\Core\Translation::lbl(
            'Web server max upload file size limit is X',
            ['size' => "{$fileSizeLimitInMB} MB"]
        );
    }

    /**
     * Get human readable file size help
     *
     * @return string
     */
    protected function getFileSizeHelp()
    {
        return \XLite\Core\Translation::lbl(
            'The maximum size of the uploaded file is limited by the following parameters in your server settings: X and Y',
            [
                'post_max_size'       => Converter::convertShortSizeToHumanReadable(ini_get('post_max_size')),
                'upload_max_filesize' => Converter::convertShortSizeToHumanReadable(ini_get('upload_max_filesize')),
            ]
        );
    }
} 