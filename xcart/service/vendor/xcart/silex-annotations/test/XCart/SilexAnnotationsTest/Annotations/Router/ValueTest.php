<?php
namespace XCart\SilexAnnotationsTest\Annotations\Router;

use XCart\SilexAnnotationsTest\RoutesAnnotationsTestBase;

class ValueTest extends RoutesAnnotationsTestBase
{
    public function testDefaultValue()
    {
        $this->assertEndPointStatus(self::GET_METHOD, '/foo', self::STATUS_OK);

        $response = $this->makeRequest(self::GET_METHOD, '/');
        $this->assertStatus($response, self::STATUS_OK);
        $this->assertEquals('default', $response->getContent());
    }
}
