<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core;

use XCart\SilexAnnotations\SilexAnnotationsException;

class ResolverAnnotationException extends SilexAnnotationsException
{
    /**
     * @param string $className
     * @param string $methodName
     *
     * @return ResolverAnnotationException
     */
    public static function fromNotAService($className, $methodName)
    {
        return new self(
            sprintf(
                '@Resolver annotation used in not a service class %s::%s',
                $className,
                $methodName
            )
        );
    }
}
