<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core\Annotations;

use Pimple\Exception\FrozenServiceException;
use ReflectionClass;
use ReflectionMethod;
use Silex\Application;
use XCart\Bus\Core\ResolverAnnotationException;
use XCart\SilexAnnotations\Annotations\Service\AService;
use XCart\SilexAnnotations\SilexAnnotationsException;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Resolver
{
    /** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param Application      $app
     * @param array            $classAnnotations
     * @param ReflectionClass  $reflectionClass
     * @param ReflectionMethod $reflectionMethod
     *
     * @throws FrozenServiceException
     * @throws ResolverAnnotationException
     * @throws SilexAnnotationsException
     */
    public function process(
        Application $app,
        array $classAnnotations,
        ReflectionClass $reflectionClass,
        ReflectionMethod $reflectionMethod
    ): void {
        $object      = null;
        $serviceName = null;
        foreach ($classAnnotations as $annotation) {
            if ($annotation instanceof AService) {
                $serviceName = $annotation->name;
                $object      = $app[$annotation->name];

                break;
            }
        }

        if ($object === null) {
            throw ResolverAnnotationException::fromNotAService(
                $reflectionClass->getName(),
                $reflectionMethod->getName()
            );
        }

        $app->offsetSet(
            $serviceName . ':' . $reflectionMethod->getName(),
            static function () use ($object, $reflectionMethod) {
                return $reflectionMethod->getClosure($object);
            }
        );
    }
}
