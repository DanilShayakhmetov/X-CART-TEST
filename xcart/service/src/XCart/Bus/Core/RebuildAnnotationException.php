<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core;

use XCart\SilexAnnotations\SilexAnnotationsException;

class RebuildAnnotationException extends SilexAnnotationsException
{
    /**
     * @param string $className
     * @param string $methodName
     *
     * @return RebuildAnnotationException
     */
    public static function fromNotFactory($className, $methodName)
    {
        return new self(
            sprintf(
                '@Resolver annotation used in not a factory class %s::%s',
                $className,
                $methodName
            )
        );
    }
}
