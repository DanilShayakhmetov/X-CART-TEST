<?php

namespace XCart\SilexAnnotationsTest;

use Doctrine\Common\Cache\ArrayCache;
use PHPUnit\Framework\TestCase;
use Silex\Application;
use XCart\SilexAnnotations\AnnotationServiceProvider;
use XCart\SilexAnnotations\SilexAnnotationsException;
use XCart\SilexAnnotationsTest\Fixtures\AnnotationsSample\SomeAnnotationService;

class AnnotationServiceProviderTest extends TestCase
{
    public function testBootCached()
    {
        $cacheSpy = \Mockery::spy(new ArrayCache())->makePartial();

        $app          = new Application();
        $app['debug'] = true;

        $app->register(new AnnotationServiceProvider(), [
            AnnotationServiceProvider::ROOT_OPTION_NAME     => __DIR__ . '/../../',
            AnnotationServiceProvider::CACHE_SERVICE_NAME   => $cacheSpy,
            AnnotationServiceProvider::SERVICES_OPTION_NAME => [
                SomeAnnotationService::class => __DIR__ . '/Fixtures/AnnotationsSample/Sample',
            ],
        ]);

        $app->boot();

        $app          = new Application();
        $app['debug'] = true;

        $app->register(new AnnotationServiceProvider(), [
            AnnotationServiceProvider::ROOT_OPTION_NAME     => __DIR__ . '/../../',
            AnnotationServiceProvider::CACHE_SERVICE_NAME   => $cacheSpy,
            AnnotationServiceProvider::SERVICES_OPTION_NAME => [
                SomeAnnotationService::class => __DIR__ . '/Fixtures/AnnotationsSample/Sample',
            ],
        ]);

        $app->boot();

        $app          = new Application();
        $app['debug'] = true;

        $app->register(new AnnotationServiceProvider(), [
            AnnotationServiceProvider::ROOT_OPTION_NAME     => __DIR__ . '/../../',
            AnnotationServiceProvider::CACHE_SERVICE_NAME   => $cacheSpy,
            AnnotationServiceProvider::SERVICES_OPTION_NAME => [
                SomeAnnotationService::class => __DIR__ . '/Fixtures/AnnotationsSample/Sample',
            ],
        ]);

        $app->boot();

        $cacheSpy->shouldHaveReceived('save')->twice();
        $cacheSpy->shouldHaveReceived('fetch')->times(5);
    }

    public function testWrongAnnotationService()
    {
        $app          = new Application();
        $app['debug'] = true;

        $app->register(new AnnotationServiceProvider(), [
            AnnotationServiceProvider::ROOT_OPTION_NAME     => __DIR__ . '/../../',
            AnnotationServiceProvider::SERVICES_OPTION_NAME => [
                'Some\Missing\Class' => __DIR__,
            ],
        ]);

        $this->expectException(SilexAnnotationsException::class);
        $this->expectExceptionMessageRegExp('/^Can\'t instantiate annotation service:/');

        $app->boot();
    }
}
