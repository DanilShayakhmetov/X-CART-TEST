<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View;

use XLite\Model\WidgetParam\TypeString;

class InlineFileUploader extends \XLite\View\FileUploader
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
    protected function getDefaultTemplate()
    {
        return '/modules/XC/Onboarding/file_uploader/body.twig';
    }

    /**
     * @return string
     */
    protected function getDivStyle()
    {
        return parent::getDivStyle() . ' inline-uploader';
    }

    /**
     * @return array|string[]
     */
    public function getCSSFiles()
    {
        $css   = parent::getCSSFiles();
        $css[] = 'modules/XC/Onboarding/file_uploader/style.less';

        return $css;
    }

    /**
     * @return mixed
     */
    protected function getUploadedMessage()
    {
        return $this->getParam(static::PARAM_UPLOADED_MESSAGE);
    }

    /**
     * @return string
     */
    protected function getUploadingTarget()
    {
        return 'onboarding_files';
    }
}