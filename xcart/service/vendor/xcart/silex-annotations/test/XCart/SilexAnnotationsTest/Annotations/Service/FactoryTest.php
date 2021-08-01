<?php

namespace XCart\SilexAnnotationsTest\Annotations\Service;

use PHPUnit\Framework\TestCase;
use Silex\Application;
use XCart\SilexAnnotations\AnnotationServiceProvider;
use XCart\SilexAnnotations\ServiceAnnotationService;

class FactoryTest extends TestCase
{
    public function testFactory()
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

        $this->assertTrue($app->offsetExists('x_cart.silex_annotations_test.fixtures.service_annotation_sample.simple_factory'));

        $instance1 = $app['x_cart.silex_annotations_test.fixtures.service_annotation_sample.simple_factory'];
        $instance2 = $app['x_cart.silex_annotations_test.fixtures.service_annotation_sample.simple_factory'];

        $this->assertNotSame($instance1, $instance2);
    }

    public function testConstructor()
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

        $this->assertTrue($app->offsetExists('x_cart.silex_annotations_test.fixtures.service_annotation_sample.simple_factory'));

        $instance = $app['x_cart.silex_annotations_test.fixtures.service_annotation_sample.simple_factory'];

        $this->assertSame($app, $instance->app);
    }
}
