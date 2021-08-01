<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\Controller\Admin;

use XLite\Controller\Admin\Files;
use XLite\Core\Request;
use XLite\Model\TemporaryFile;
use XLite\Module\XC\Onboarding\View\InlineFileUploader;
use XLite\View\FileUploader;

class OnboardingFiles extends Files
{
    /**
     * Return content
     *
     * @param mixed  $file    File
     * @param string $message Message OPTIONAL
     *
     * @return void
     */
    protected function getContent($file, $message = '')
    {
        $headers = $this->getAdditionalHeaders();
        if ($message) {
            $headers['X-Upload-Error'] = $message;
        }
        static::sendHeaders($headers);

        $request = Request::getInstance();

        $viewer = new InlineFileUploader([
            FileUploader::PARAM_NAME         => $request->name,
            FileUploader::PARAM_MULTIPLE     => $request->multiple,
            FileUploader::PARAM_OBJECT       => $file,
            FileUploader::PARAM_OBJECT_ID    => $request->object_id,
            FileUploader::PARAM_MESSAGE      => $message,
            FileUploader::PARAM_IS_TEMPORARY => true,
            FileUploader::PARAM_MAX_WIDTH    => $request->max_width,
            FileUploader::PARAM_MAX_HEIGHT   => $request->max_height,
            FileUploader::PARAM_IS_IMAGE     => $file instanceof TemporaryFile
                ? $request->is_image
                : null,
        ]);

        $this->printAJAXOutput($viewer);
        exit(0);
    }
}