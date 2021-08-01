<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations\Annotations\Service;

use ReflectionClass;
use Silex\Application;

abstract class AMethod
{
    /**
     * @param Application     $app
     * @param ReflectionClass $reflectionClass
     *
     * @return mixed
     */
    abstract public function process(Application $app, ReflectionClass $reflectionClass);
}
