<?php
namespace XCart\SilexAnnotationsTest\Annotations\Router;

use XCart\SilexAnnotationsTest\RoutesAnnotationsTestBase;

class RouteTest extends RoutesAnnotationsTestBase
{
    public function testMultipleRoutes()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/test/route/45', self::STATUS_OK);

        $this->assertEndPointStatus(self::GET_METHOD, '/test/route2/45', self::STATUS_OK);
    }

    public function testIsolationOfModifiers()
    {
        // The assert should not be applied to the route2 uri, so a string for {var} should match the route.
        $this->assertEndPointStatus(self::GET_METHOD, '/test/route2/string', self::STATUS_OK);
    }
}
