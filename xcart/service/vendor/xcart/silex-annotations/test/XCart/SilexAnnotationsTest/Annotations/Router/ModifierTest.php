<?php

namespace XCart\SilexAnnotationsTest\Annotations\Router;

use XCart\SilexAnnotationsTest\RoutesAnnotationsTestBase;
use Symfony\Component\HttpFoundation\Request;

class ModifierTest extends RoutesAnnotationsTestBase
{
    public function testHostOneArg()
    {
        // testing a modifier that has one argument
        $this->assertEndPointStatus(self::GET_METHOD, '/test/host/modifier', self::STATUS_NOT_FOUND);
    }

    public function testAssertMultipleArgs()
    {
        // testing a modifier that has more than one argument
        $this->assertEndPointStatus(self::GET_METHOD, '/test/assert/fail', self::STATUS_NOT_FOUND);
    }

    public function testHttpsNoArgs()
    {
        // testing a modifier that has no arguments
        // we make the request as http, but it should be redirected to a Http request
        $this->registerAnnotations();
        $request = Request::create('http://test.com/test/requirehttps/modifier');
        $response = $this->app->handle($request);
        $this->assertEquals(301, $response->getStatusCode());
        $this->assertTrue($response->isRedirect('https://test.com/test/requirehttps/modifier'));
    }
}
