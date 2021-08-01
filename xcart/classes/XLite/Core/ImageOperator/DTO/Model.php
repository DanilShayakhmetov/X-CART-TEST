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
class Model extends ADTO
{
    public function __construct(\XLite\Model\Base\Image $image)
    {
        $this->setBody($image->getBody());
        $this->setName($image->getFileName());
        $this->setType($image->getMime());
        $this->setWidth($image->getWidth());
        $this->setHeight($image->getHeight());
    }
}
