<?php

namespace XCart\SilexAnnotationsTest\Annotations\Router;

use XCart\SilexAnnotationsTest\RoutesAnnotationsTestBase;
use Symfony\Component\HttpFoundation\Request;

class RequireHttpTest extends RoutesAnnotationsTestBase
{
    public function testHttp()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/test/requirehttp', self::STATUS_OK);
    }

    public function testHttps()
    {
        // we make the request as https, but it should be redirected to a http request
        $this->registerAnnotations();
        $request = Request::create('https://test.com/test/requirehttp');
        $response = $this->app->handle($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('http://test.com/test/requirehttp'));
    }

    public function testHttpCollection()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/requirehttp/test', self::STATUS_OK);
    }

    public function testHttpsCollection()
    {
        // we make the request as https, but it should be redirected to a http request
        $this->registerAnnotations();
        $request = Request::create('https://test.com/requirehttp/test');
        $response = $this->app->handle($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('http://test.com/requirehttp/test'));
    }
}
