<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations\Annotations\Router;

use Doctrine\Common\Annotations\Reader;
use Pimple\Exception\UnknownIdentifierException;
use ReflectionClass;
use ReflectionMethod;
use Silex\Application;
use Silex\ControllerCollection;
use XCart\SilexAnnotations\AnnotationServiceProvider;
use XCart\SilexAnnotations\NameConverter\INameConverter;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Controller implements IController
{
    /**
     * @var string
     */
    public $prefix;

    /**
     * @param Application     $app
     * @param ReflectionClass $reflectionClass
     *
     * @throws \LogicException
     */
    public function process(Application $app, ReflectionClass $reflectionClass)
    {
        $controllerCollection = $app['controllers_factory'];

        $this->processClass($app, $reflectionClass, $controllerCollection);
        $this->processMethods($app, $reflectionClass, $controllerCollection);

        $app->mount($this->prefix, $controllerCollection);
    }

    /**
     * @param Application          $app
     * @param ReflectionClass      $reflectionClass
     * @param ControllerCollection $controllerCollection
     */
    protected function processClass(
        Application $app,
        ReflectionClass $reflectionClass,
        ControllerCollection $controllerCollection
    )
    {
        /** @var Reader $reader */
        $reader = $app[AnnotationServiceProvider::READER_SERVICE_NAME];

        foreach ($reader->getClassAnnotations($reflectionClass) as $annotation) {
            if ($annotation instanceof IRoute) {
                $annotation->process($controllerCollection);
            }
        }
    }

    /**
     * @param Application          $app
     * @param ReflectionClass      $reflectionClass
     * @param ControllerCollection $controllerCollection
     */
    protected function processMethods(
        Application $app,
        ReflectionClass $reflectionClass,
        ControllerCollection $controllerCollection
    )
    {
        /** @var Reader $reader */
        $reader = $app[AnnotationServiceProvider::READER_SERVICE_NAME];

        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            if ($reflectionMethod->isStatic()) {
                continue;
            }

            $handlerName       = $this->getRouteHandlerName($app, $reflectionClass->getName(), $reflectionMethod->getName());
            $methodAnnotations = $reader->getMethodAnnotations($reflectionMethod);

            foreach ($methodAnnotations as $annotation) {
                if ($annotation instanceof Route) {
                    $annotation->process($controllerCollection, $handlerName);

                } elseif ($annotation instanceof Request) {
                    $controller = $annotation->process($controllerCollection, $handlerName);
                    foreach ($methodAnnotations as $routeAnnotation) {
                        if ($routeAnnotation instanceof IRoute) {
                            $routeAnnotation->process($controller);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param Application $app
     * @param string      $className
     * @param string      $methodName
     *
     * @return string
     * @throws UnknownIdentifierException
     */
    protected function getRouteHandlerName(Application $app, $className, $methodName)
    {
        /** @var INameConverter $nameConverter */
        $nameConverter = $app->offsetGet(AnnotationServiceProvider::NAME_CONVERTER_SERVICE_NAME);

        $serviceName = $nameConverter->classNameToServiceName($className);

        if ($app->offsetExists($serviceName)) {
            return $serviceName . ':' . $methodName;
        }

        return $className . '::' . $methodName;
    }

    /**
     * @param $className
     *
     * @return string
     */
    protected function convertToServiceName($className)
    {
        return strtolower(preg_replace_callback(
            '/([a-z])([A-Z])/',
            function ($matches) {
                return $matches[1] . '_' . $matches[2];
            },
            str_replace('\\', '.', $className)
        ));
    }
}
