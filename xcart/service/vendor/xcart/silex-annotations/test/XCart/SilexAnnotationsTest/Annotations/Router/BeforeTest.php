<?php

namespace XCart\SilexAnnotationsTest\Annotations\Router;

use XCart\SilexAnnotationsTest\RoutesAnnotationsTestBase;

class BeforeTest extends RoutesAnnotationsTestBase
{
    public function testBefore()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/test/before', self::STATUS_ERROR);
    }

    public function testBeforeCollection()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/before/test', self::STATUS_ERROR);
    }
}
