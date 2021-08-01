<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations\NameConverter;

class DotNotation implements INameConverter
{
    /**
     * @param string $className
     *
     * @return string
     */
    public function classNameToServiceName($className)
    {
        return strtolower(preg_replace_callback(
            ['/([a-z])([A-Z])/S', '/([A-Z])([A-Z][a-z])/S'],
            function ($matches) {
                return $matches[1] . '_' . $matches[2];
            },
            str_replace('\\', '.', trim($className, '\\'))
        ));
    }
}
