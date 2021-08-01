<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations\Annotations\Router;

use ReflectionClass;
use Silex\Application;

interface IController
{
    /**
     * @param Application     $app
     * @param ReflectionClass $reflectionClass
     */
    public function process(Application $app, ReflectionClass $reflectionClass);
}
