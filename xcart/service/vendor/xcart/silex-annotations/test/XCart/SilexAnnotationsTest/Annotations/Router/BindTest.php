<?php

namespace XCart\SilexAnnotationsTest\Annotations\Router;

use XCart\SilexAnnotationsTest\RoutesAnnotationsTestBase;

class BindTest extends RoutesAnnotationsTestBase
{
    public function testBind()
    {
        $response = $this->makeRequest(self::GET_METHOD, '/test/bind');
        $this->assertEquals('/test/bind', $response->getContent());
    }
}

