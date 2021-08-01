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
 * @Target("CLASS")
 */
class Factory extends AService
{
    protected function getServiceConstructor(Application $app, ReflectionClass $reflectionClass)
    {
        return $app->factory(parent::getServiceConstructor($app, $reflectionClass));
    }
}
