<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations\NameConverter;

interface INameConverter
{
    /**
     * @param string $className
     *
     * @return string
     */
    public function classNameToServiceName($className);
}
