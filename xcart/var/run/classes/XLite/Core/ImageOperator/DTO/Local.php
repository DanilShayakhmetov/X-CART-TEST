<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ImageOperator\DTO;

use XLite\Core\ImageOperator\ADTO;

/**
 * Model
 */
class Local extends ADTO
{
    public function __construct($path)
    {
        $this->setBody(\Includes\Utils\FileManager::read($path));

        $imageSize = @getimagesizefromstring($this->getBody());
        if (is_array($imageSize)) {
            $this->setName(basename($path));
            $this->setWidth($imageSize[0]);
            $this->setHeight($imageSize[1]);
            $this->setType($imageSize['mime']);
        }
    }
}
