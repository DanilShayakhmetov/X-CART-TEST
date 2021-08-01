<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ImageOperator\DTO;

use XLite\Core\ImageOperator\ADTO;
use XLite\Core\RemoteResource\IURL;

/**
 * Model
 */
class Remote extends ADTO
{
    public function __construct(IURL $remoteResource)
    {
        if ($remoteResource->isAvailable()) {
            $body = $remoteResource instanceof \XLite\Core\RemoteResource\Local
                ? \Includes\Utils\FileManager::read($remoteResource->getPath())
                : \XLite\Core\Operator::getURLContent($remoteResource->getURL());

            $this->setBody($body);
        }

        $imageSize = @getimagesizefromstring($this->getBody());
        if (is_array($imageSize)) {
            $this->setName($remoteResource->getName());
            $this->setWidth($imageSize[0]);
            $this->setHeight($imageSize[1]);
            $this->setType($imageSize['mime']);
        }
    }
}
