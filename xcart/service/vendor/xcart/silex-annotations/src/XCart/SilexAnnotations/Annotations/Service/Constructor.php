<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations\Annotations\Service;

use ReflectionClass;
use Silex\Application;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Constructor extends AMethod
{
    /**
     * @param Application     $app
     * @param ReflectionClass $reflectionClass
     *
     * @return mixed
     */
    public function process(Application $app, ReflectionClass $reflectionClass)
    {
        return null;
    }
}
