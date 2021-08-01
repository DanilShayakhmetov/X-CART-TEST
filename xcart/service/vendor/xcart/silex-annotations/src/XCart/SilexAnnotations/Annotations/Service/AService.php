<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations\Annotations\Service;

use Doctrine\Common\Annotations\Reader;
use Pimple\Exception\ExpectedInvokableException;
use Pimple\Exception\FrozenServiceException;
use Pimple\Exception\UnknownIdentifierException;
use ReflectionClass;
use ReflectionMethod;
use Silex\Application;
use XCart\SilexAnnotations\AnnotationServiceProvider;
use XCart\SilexAnnotations\NameConverter\INameConverter;
use XCart\SilexAnnotations\ServiceAnnotation\ArgumentsMapper;
use XCart\SilexAnnotations\ServiceAnnotationService;

abstract class AService
{
    public $name;

    public $arguments = [];

    /**
     * @param Application     $app
     * @param ReflectionClass $reflectionClass
     *
     * @throws FrozenServiceException
     * @throws ExpectedInvokableException
     * @throws UnknownIdentifierException
     */
    public function process(Application $app, ReflectionClass $reflectionClass)
    {
        if (!$this->name) {
            /** @var INameConverter $nameConverter */
            $nameConverter = $app[AnnotationServiceProvider::NAME_CONVERTER_SERVICE_NAME];

            $this->name = $nameConverter->classNameToServiceName($reflectionClass->getName());
        }

        $app->offsetSet(
            $this->name,
            $this->getServiceConstructor($app, $reflectionClass)
        );
    }

    /**
     * @param Application     $app
     * @param ReflectionClass $reflectionClass
     *
     * @return \Closure
     * @throws UnknownIdentifierException
     */
    protected function getServiceConstructor(Application $app, ReflectionClass $reflectionClass)
    {
        /** @var Reader $reader */
        $reader = $app[AnnotationServiceProvider::READER_SERVICE_NAME];
        /** @var ArgumentsMapper $argumentsMapper */
        $argumentsMapper         = $app[ServiceAnnotationService::ARGUMENT_MAPPER_SERVICE_NAME];
        $classServiceConstructor = $this->getClassServiceConstructor($reflectionClass, $reader);

        if ($classServiceConstructor) {
            return function () use ($classServiceConstructor, $argumentsMapper) {
                return $classServiceConstructor->invokeArgs(
                    null,
                    $argumentsMapper->getValues($argumentsMapper->getArguments($classServiceConstructor, $this->arguments))
                );
            };
        }

        return $this->getDefaultConstructor($app, $reflectionClass);
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param Reader          $reader
     *
     * @return ReflectionMethod
     */
    protected function getClassServiceConstructor(ReflectionClass $reflectionClass, Reader $reader)
    {
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_STATIC) as $method) {
            foreach ($reader->getMethodAnnotations($method) as $annotation) {
                if ($annotation instanceof Constructor) {
                    return $method;
                }
            }
        }

        return null;
    }

    /**
     * @param Application     $app
     * @param ReflectionClass $reflectionClass
     *
     * @return \Closure
     */
    protected function getDefaultConstructor(Application $app, ReflectionClass $reflectionClass)
    {
        /** @var ArgumentsMapper $argumentsMapper */
        $argumentsMapper = $app[ServiceAnnotationService::ARGUMENT_MAPPER_SERVICE_NAME];

        return function () use ($reflectionClass, $argumentsMapper) {
            $args = $argumentsMapper->getValues(
                $this->getDefaultConstructorArgs($reflectionClass, $argumentsMapper)
            );

            return $args ? $reflectionClass->newInstanceArgs($args) : $reflectionClass->newInstance();
        };
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @param ArgumentsMapper $argumentsMapper
     *
     * @return array
     */
    protected function getDefaultConstructorArgs(ReflectionClass $reflectionClass, ArgumentsMapper $argumentsMapper)
    {
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isConstructor()) {
                return $argumentsMapper->getArguments($method, $this->arguments);
            }
        }

        return [];
    }
}
