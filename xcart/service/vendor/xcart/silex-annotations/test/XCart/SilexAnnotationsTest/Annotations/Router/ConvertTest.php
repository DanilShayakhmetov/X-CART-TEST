<?php

namespace XCart\SilexAnnotationsTest\Annotations\Router;

use XCart\SilexAnnotationsTest\RoutesAnnotationsTestBase;

class ConvertTest extends RoutesAnnotationsTestBase
{
    public function testConvert()
    {
        $response = $this->makeRequest(self::GET_METHOD, '/test/convert/45');
        $this->assertStatus($response, self::STATUS_OK);
        $this->assertEquals('50', $response->getContent());
    }

    public function testConvertCollection()
    {
        $response = $this->makeRequest(self::GET_METHOD, '/convert/test/45');
        $this->assertStatus($response, self::STATUS_OK);
        $this->assertEquals('50', $response->getContent());
    }
}
