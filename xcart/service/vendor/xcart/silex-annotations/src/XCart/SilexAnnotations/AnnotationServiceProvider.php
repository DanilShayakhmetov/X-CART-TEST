<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Cache\Cache;
use Pimple\Container;
use Pimple\Exception\FrozenServiceException;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use XCart\SilexAnnotations\NameConverter\DotNotation;

class AnnotationServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    const ROOT_OPTION_NAME     = 'x_cart.silex_annotations.root';
    const SERVICES_OPTION_NAME = 'x_cart.silex_annotations.services';

    const READER_SERVICE_NAME = 'x_cart.silex_annotations.reader';
    const CACHE_SERVICE_NAME  = 'x_cart.silex_annotations.cache';

    const NAME_CONVERTER_SERVICE_NAME = 'x_cart.silex_annotations.name_converter';

    /**
     * @param Application $app
     *
     * @throws FrozenServiceException
     * @throws SilexAnnotationsException
     */
    public function boot(Application $app)
    {
        if (!$app->offsetExists(self::NAME_CONVERTER_SERVICE_NAME)) {
            $app->offsetSet(self::NAME_CONVERTER_SERVICE_NAME, function () {
                return new DotNotation();
            });
        }

        $classLocator = $this->createClassLocator($app);

        try {
            $app->offsetSet(self::READER_SERVICE_NAME, function (Application $app) {

                return $this->createAnnotationReader($app);
            });

            /** @var Reader $reader */
            $reader = $app[self::READER_SERVICE_NAME];

        } catch (FrozenServiceException $e) {

            throw SilexAnnotationsException::fromFrozenService(self::READER_SERVICE_NAME);

        } catch (AnnotationException $e) {

            throw SilexAnnotationsException::fromReaderInstantiation($e->getMessage());
        }

        /**
         * @var string          $class
         * @var string|string[] $locations
         */
        foreach ((array) $app[self::SERVICES_OPTION_NAME] as $class => $locations) {
            if (!class_exists($class)) {

                throw SilexAnnotationsException::fromServiceInstantiation($class);
            }

            /** @var IAnnotationService $service */
            $service = new $class($app, $reader);

            if ($service instanceof BootableProviderInterface) {
                $service->boot($app);
            }

            $service->register($classLocator->getClasses($locations));
        }
    }

    /**
     * @param Container $app
     *
     * @throws FrozenServiceException
     */
    public function register(Container $app)
    {
        AnnotationRegistry::registerLoader('class_exists');
    }

    /**
     * @param Application $app
     *
     * @return Reader
     * @throws AnnotationException
     */
    private function createAnnotationReader(Application $app)
    {
        if ($app->offsetExists(self::CACHE_SERVICE_NAME)) {
            $cache = $app[self::CACHE_SERVICE_NAME];

            if ($cache instanceof Cache) {

                return new CachedReader(new AnnotationReader(), $cache, $app['debug']);
            }
        }

        return new AnnotationReader();
    }

    /**
     * @param Application $app
     *
     * @return ClassLocator
     * @throws SilexAnnotationsException
     */
    private function createClassLocator(Application $app)
    {
        if (!$app->offsetExists(self::ROOT_OPTION_NAME)) {

            throw SilexAnnotationsException::fromMissingRequiredConfiguration(self::ROOT_OPTION_NAME);
        }

        return new ClassLocator($app[self::ROOT_OPTION_NAME]);
    }
}