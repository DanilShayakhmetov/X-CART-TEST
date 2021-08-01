<?php

namespace XCart\SilexAnnotationsTest\Annotations\Router;

use XCart\SilexAnnotationsTest\RoutesAnnotationsTestBase;

class AssertTest extends RoutesAnnotationsTestBase
{
    public function testAssert()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/test/assert/45', self::STATUS_OK);
        $this->assertEndPointStatus(self::GET_METHOD, '/test/assert/fail', self::STATUS_NOT_FOUND);
    }

    public function testAssertCollection()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/assert/test/45', self::STATUS_OK);
        $this->assertEndPointStatus(self::GET_METHOD, '/assert/test/fail', self::STATUS_NOT_FOUND);
    }
}
