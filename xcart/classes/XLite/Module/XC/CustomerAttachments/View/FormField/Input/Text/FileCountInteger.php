<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomerAttachments\View\FormField\Input\Text;

/**
 * Field for file size
 */
class FileCountInteger extends \XLite\View\FormField\Input\Text\Integer
{
    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[static::PARAM_COMMENT]->setValue($this->getFileCountComment());
        $this->widgetParams[static::PARAM_MAX]->setValue(
            ini_get('max_file_uploads')
        );
    }

    /**
     * Get human readable file size comment
     *
     * @return string
     */
    protected function getFileCountComment()
    {
        return \XLite\Core\Translation::lbl(
            'Web server max number of files limit is X',
            ['count' => ini_get('max_file_uploads')]
        );
    }
} 