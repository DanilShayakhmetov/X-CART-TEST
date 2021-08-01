<?php
namespace XCart\SilexAnnotationsTest\Annotations\Router;

use XCart\SilexAnnotationsTest\Fixtures\Controller\ServiceTestController;
use XCart\SilexAnnotationsTest\RoutesAnnotationsTestBase;

class ServiceTest extends RoutesAnnotationsTestBase
{
    public function testDefaultValue()
    {
        $response = $this->makeRequest(self::GET_METHOD, '/service');
        $this->assertStatus($response, self::STATUS_OK);
        $this->assertEquals(ServiceTestController::class, $response->getContent());
    }
}
