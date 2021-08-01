<?php

namespace XCart\SilexAnnotationsTest;

use PHPUnit\Framework\TestCase;
use Silex\Application;
use XCart\SilexAnnotations\AnnotationServiceProvider;
use XCart\SilexAnnotations\ServiceAnnotationService;
use XCart\SilexAnnotations\SilexAnnotationsException;

class ServiceAnnotationTest extends TestCase
{
    public function testBoot()
    {
        $app          = new Application();
        $app['debug'] = true;

        $app->register(new AnnotationServiceProvider(), [
            AnnotationServiceProvider::ROOT_OPTION_NAME     => __DIR__ . '/../../',
            AnnotationServiceProvider::SERVICES_OPTION_NAME => [
                ServiceAnnotationService::class => __DIR__ . '/Fixtures/ServiceAnnotationSample',
            ],
        ]);

        $app->boot();

        $this->assertTrue($app->offsetExists('x_cart.silex_annotations_test.fixtures.service_annotation_sample.simple_service'));
        $this->assertTrue($app->offsetExists('x_cart.silex_annotations_test.fixtures.service_annotation_sample.simple_factory'));
    }

    public function testMissingConfiguration()
    {
        $app          = new Application();
        $app['debug'] = true;

        $app->register(new AnnotationServiceProvider(), [
            AnnotationServiceProvider::SERVICES_OPTION_NAME => [
                ServiceAnnotationService::class => __DIR__ . '/Fixtures/ServiceAnnotationSample',
            ],
        ]);

        $this->expectException(SilexAnnotationsException::class);
        $this->expectExceptionMessageRegExp('/^Configuration error\. .* option in required\./');

        $app->boot();
    }

    public function testFrozenReader()
    {
        $app          = new Application();
        $app['debug'] = true;

        $app->register(new AnnotationServiceProvider(), [
            AnnotationServiceProvider::ROOT_OPTION_NAME     => __DIR__ . '/../../',
            AnnotationServiceProvider::SERVICES_OPTION_NAME => [
                ServiceAnnotationService::class => __DIR__ . '/Fixtures/ServiceAnnotationSample',
            ],
        ]);

        $app[AnnotationServiceProvider::READER_SERVICE_NAME] = function () { return 1; };
        $app->offsetGet(AnnotationServiceProvider::READER_SERVICE_NAME);

        $this->expectException(SilexAnnotationsException::class);
        $this->expectExceptionMessageRegExp('/^Can\'t redeclare frozen service/');

        $app->boot();
    }
}
