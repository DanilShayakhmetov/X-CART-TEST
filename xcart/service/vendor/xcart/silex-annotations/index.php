<?php

use Silex\Application;
use XCart\SilexAnnotations\AnnotationServiceProvider;
use XCart\SilexAnnotations\ServiceAnnotationService;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application();
$app['debug'] = true;

$app->register(new AnnotationServiceProvider(), [
    'xcart.silex_annotations.root' => __DIR__ . '/test',
    'xcart.silex_annotations.services' => [
        ServiceAnnotationService::class => __DIR__ . '/test/XCart/SilexAnnotationsTest/ServiceAnnotationSample',
    ]
]);

$app->boot();
