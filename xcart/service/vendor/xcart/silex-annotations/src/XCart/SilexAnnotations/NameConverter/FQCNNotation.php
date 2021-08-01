<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations\NameConverter;

class FQCNNotation implements INameConverter
{
    /**
     * @param string $className
     *
     * @return string
     */
    public function classNameToServiceName($className)
    {
        return trim($className, '\\');
    }
}
