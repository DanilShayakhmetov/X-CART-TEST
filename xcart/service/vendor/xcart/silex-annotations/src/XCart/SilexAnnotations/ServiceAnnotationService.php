<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations;

use Pimple\Exception\FrozenServiceException;
use Pimple\Exception\UnknownIdentifierException;
use ReflectionClass;
use ReflectionException;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use XCart\SilexAnnotations\Annotations\Service\AService;
use XCart\SilexAnnotations\ServiceAnnotation\ArgumentsMapper;

class ServiceAnnotationService extends AAnnotationService implements BootableProviderInterface
{
    const ARGUMENT_MAPPINGS = 'x_cart.service_annotation_service.argument_mappings';

    const ARGUMENT_MAPPER_SERVICE_NAME = 'x_cart.silex_annotations.service_annotation.arguments_mapper';

    /**
     * @param string $class
     *
     * @throws ReflectionException
     */
    protected function registerService($class)
    {
        $reflectionClass  = new ReflectionClass($class);
        $classAnnotations = $this->reader->getClassAnnotations($reflectionClass);

        foreach ($classAnnotations as $annotation) {
            if ($annotation instanceof AService) {
                $annotation->process($this->app, $reflectionClass);
            }
        }
    }

    /**
     * @param Application $app
     *
     * @throws UnknownIdentifierException
     * @throws FrozenServiceException
     */
    public function boot(Application $app)
    {
        $app->offsetSet(self::ARGUMENT_MAPPER_SERVICE_NAME, function (Application $app) {
            return new ArgumentsMapper(
                $app,
                $app->offsetGet(AnnotationServiceProvider::NAME_CONVERTER_SERVICE_NAME),
                $app->offsetExists(self::ARGUMENT_MAPPINGS)
                    ? $app->offsetGet(self::ARGUMENT_MAPPINGS)
                    : []
            );
        });
    }
}