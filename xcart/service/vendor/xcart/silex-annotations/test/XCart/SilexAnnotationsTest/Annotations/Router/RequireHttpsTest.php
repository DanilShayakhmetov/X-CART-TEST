<?php

namespace XCart\SilexAnnotationsTest\Annotations\Router;

use XCart\SilexAnnotationsTest\RoutesAnnotationsTestBase;
use Symfony\Component\HttpFoundation\Request;

class RequireHttpsTest extends RoutesAnnotationsTestBase
{
    public function testHttps()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/test/requirehttps', self::STATUS_OK);
    }

    public function testHttp()
    {
        // we make the request as http, but it should be redirected to a https request
        $this->registerAnnotations();
        $request = Request::create('https://test.com/test/requirehttps');
        $response = $this->app->handle($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('http://test.com/test/requirehttps'));
    }

    public function testHttpsCollection()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/test/requirehttps', self::STATUS_OK);
    }

    public function testHttpCollection()
    {
        // we make the request as http, but it should be redirected to a https request
        $this->registerAnnotations();
        $request = Request::create('https://test.com/test/requirehttps');
        $response = $this->app->handle($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('http://test.com/test/requirehttps'));
    }
}
