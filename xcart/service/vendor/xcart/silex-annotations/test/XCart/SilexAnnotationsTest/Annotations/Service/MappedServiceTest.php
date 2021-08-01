<?php

namespace XCart\SilexAnnotationsTest\Annotations\Service;

use PHPUnit\Framework\TestCase;
use Silex\Application;
use XCart\SilexAnnotations\AnnotationServiceProvider;
use XCart\SilexAnnotations\ServiceAnnotationService;
use XCart\SilexAnnotationsTest\Fixtures\ServiceAnnotationSample\MappedService;
use XCart\SilexAnnotationsTest\Fixtures\ServiceAnnotationSample\MappingService;

class MappedServiceTest extends TestCase
{
    public function testService()
    {
        $app          = new Application();
        $app['debug'] = true;

        $app->register(new AnnotationServiceProvider(), [
            AnnotationServiceProvider::ROOT_OPTION_NAME     => __DIR__ . '/../../../../',
            AnnotationServiceProvider::SERVICES_OPTION_NAME => [
                ServiceAnnotationService::class => __DIR__ . '/../../Fixtures/ServiceAnnotationSample',
            ],
        ]);

        $app->boot();

        $this->assertTrue($app->offsetExists('x_cart.silex_annotations_test.fixtures.service_annotation_sample.without_mapping_service'));

        $instance = $app['x_cart.silex_annotations_test.fixtures.service_annotation_sample.without_mapping_service'];

        $this->assertInstanceOf(MappedService::class, $instance->service);
        $this->assertNotInstanceOf(MappingService::class, $instance->service);
    }

    public function testServiceMappedLocal()
    {
        $app          = new Application();
        $app['debug'] = true;

        $app->register(new AnnotationServiceProvider(), [
            AnnotationServiceProvider::ROOT_OPTION_NAME     => __DIR__ . '/../../../../',
            AnnotationServiceProvider::SERVICES_OPTION_NAME => [
                ServiceAnnotationService::class => __DIR__ . '/../../Fixtures/ServiceAnnotationSample',
            ],
        ]);

        $app->boot();

        $this->assertTrue($app->offsetExists('x_cart.silex_annotations_test.fixtures.service_annotation_sample.with_mapping_service'));

        $instance = $app['x_cart.silex_annotations_test.fixtures.service_annotation_sample.with_mapping_service'];

        $this->assertInstanceOf(MappingService::class, $instance->service);
    }

    public function testServiceWithCustomConstructorMappedLocal()
    {
        $app          = new Application();
        $app['debug'] = true;

        $app->register(new AnnotationServiceProvider(), [
            AnnotationServiceProvider::ROOT_OPTION_NAME     => __DIR__ . '/../../../../',
            AnnotationServiceProvider::SERVICES_OPTION_NAME => [
                ServiceAnnotationService::class => __DIR__ . '/../../Fixtures/ServiceAnnotationSample',
            ],
        ]);

        $app->boot();

        $this->assertTrue($app->offsetExists('x_cart.silex_annotations_test.fixtures.service_annotation_sample.with_mapping_and_custom_constructor_service'));

        $instance = $app['x_cart.silex_annotations_test.fixtures.service_annotation_sample.with_mapping_and_custom_constructor_service'];

        $this->assertInstanceOf(MappingService::class, $instance->service);
    }

    public function testServiceMappedGlobal()
    {
        $app          = new Application();
        $app['debug'] = true;

        $app->register(new AnnotationServiceProvider(), [
            AnnotationServiceProvider::ROOT_OPTION_NAME     => __DIR__ . '/../../../../',
            AnnotationServiceProvider::SERVICES_OPTION_NAME => [
                ServiceAnnotationService::class => __DIR__ . '/../../Fixtures/ServiceAnnotationSample',
            ],
            ServiceAnnotationService::ARGUMENT_MAPPINGS => [
                'x_cart.silex_annotations_test.fixtures.service_annotation_sample.mapped_service' => 'x_cart.silex_annotations_test.fixtures.service_annotation_sample.mapping_service'
            ]
        ]);

        $app->boot();

        $this->assertTrue($app->offsetExists('x_cart.silex_annotations_test.fixtures.service_annotation_sample.without_mapping_service'));

        $instance = $app['x_cart.silex_annotations_test.fixtures.service_annotation_sample.without_mapping_service'];

        $this->assertInstanceOf(MappingService::class, $instance->service);
    }
}
