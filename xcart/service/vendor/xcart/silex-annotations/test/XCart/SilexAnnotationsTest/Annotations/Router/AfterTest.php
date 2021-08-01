<?php

namespace XCart\SilexAnnotationsTest\Annotations\Router;

use XCart\SilexAnnotationsTest\RoutesAnnotationsTestBase;

class AfterTest extends RoutesAnnotationsTestBase
{
    public function testAfter()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/test/after', self::STATUS_ERROR);
    }

    public function testAfterOnCollection()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/after/test', self::STATUS_ERROR);
    }
}
