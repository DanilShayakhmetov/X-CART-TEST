<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core;

use ReflectionClass;
use ReflectionMethod;
use XCart\Bus\Core\Annotations\Resolver;
use XCart\SilexAnnotations\AAnnotationService;

class ResolverAnnotationService extends AAnnotationService
{
    /**
     * @param string $class
     *
     * @throws \Exception
     */
    protected function registerService($class)
    {
        $reflectionClass  = new ReflectionClass($class);
        $classAnnotations = $this->reader->getClassAnnotations($reflectionClass);

        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isStatic()) {
                continue;
            }

            $reflectionMethod  = new ReflectionMethod($class, $method->getName());
            $methodAnnotations = $this->reader->getMethodAnnotations($reflectionMethod);

            foreach ($methodAnnotations as $annotation) {
                if ($annotation instanceof Resolver) {
                    $annotation->process($this->app, $classAnnotations, $reflectionClass, $reflectionMethod);
                }
            }
        }
    }
}
