<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations;

use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;
use XCart\SilexAnnotations\Annotations\Router\IController;
use ReflectionClass;
use ReflectionException;

class RouterAnnotationService extends AAnnotationService implements BootableProviderInterface
{
    /**
     * @param string $class
     *™
     * @throws ReflectionException
     */
    protected function registerService($class)
    {
        $reflectionClass  = new ReflectionClass($class);
        $classAnnotations = $this->reader->getClassAnnotations($reflectionClass);

        foreach ($classAnnotations as $annotation) {
            if ($annotation instanceof IController) {
                $annotation->process($this->app, $reflectionClass);
            }
        }
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
        $app->register(new ServiceControllerServiceProvider());
    }
}
