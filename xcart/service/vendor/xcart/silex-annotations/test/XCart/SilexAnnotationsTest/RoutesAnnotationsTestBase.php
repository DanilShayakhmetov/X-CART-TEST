<?php

namespace XCart\SilexAnnotationsTest;

use PHPUnit\Framework\TestCase;
use XCart\SilexAnnotations\AnnotationServiceProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;
use XCart\SilexAnnotations\RouterAnnotationService;
use XCart\SilexAnnotations\ServiceAnnotationService;

class RoutesAnnotationsTestBase extends TestCase
{
    const GET_METHOD    = 'GET';
    const POST_METHOD   = 'POST';
    const PUT_METHOD    = 'PUT';
    const DELETE_METHOD = 'DELETE';

    const STATUS_OK           = 200;
    const STATUS_REDIRECT     = 301;
    const STATUS_UNAUTHORIZED = 401;
    const STATUS_NOT_FOUND    = 404;
    const STATUS_ERROR        = 500;

    /** @var Application */
    protected $app;

    /** @var Client */
    protected $client;

    protected $clientOptions  = [];
    protected $requestOptions = [];

    public function setup()
    {
        $this->app          = new Application();
        $this->app['debug'] = true;
    }

    protected function registerAnnotations()
    {
        $this->app->register(new AnnotationServiceProvider(), [
            AnnotationServiceProvider::ROOT_OPTION_NAME     => __DIR__ . '/../../',
            AnnotationServiceProvider::SERVICES_OPTION_NAME => [
                ServiceAnnotationService::class => __DIR__ . '/Fixtures/Controller',
                RouterAnnotationService::class  => __DIR__ . '/Fixtures/Controller',
            ],
        ]);
    }

    protected function getClient()
    {
        if (!$this->app->offsetExists('x_cart.silex_annotations.router_annotation_service')) {
            $this->registerAnnotations();
        }
        $this->client = new Client($this->app, $this->clientOptions);
    }

    protected function assertEndPointStatus($method, $uri, $status)
    {
        $this->assertStatus($this->makeRequest($method, $uri), $status);
    }

    protected function makeRequest($method, $uri)
    {
        $this->getClient();

        $this->client->request($method, $uri, [], [], $this->requestOptions);

        return $this->client->getResponse();
    }

    protected function assertStatus(Response $response, $status)
    {
        $this->assertEquals($status, $response->getStatusCode());
    }
} 