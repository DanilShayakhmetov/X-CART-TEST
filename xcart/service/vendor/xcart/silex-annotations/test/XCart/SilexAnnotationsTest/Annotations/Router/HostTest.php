<?php

namespace XCart\SilexAnnotationsTest\Annotations\Router;

use XCart\SilexAnnotationsTest\RoutesAnnotationsTestBase;

class HostTest extends RoutesAnnotationsTestBase
{
    public function testCorrectHost()
    {
        $this->clientOptions = array('HTTP_HOST' => 'www.test.com');
        $this->assertEndPointStatus(self::GET_METHOD, '/test/hostTest', self::STATUS_OK);
    }

    public function testWrongHost()
    {
        $this->clientOptions = array('HTTP_HOST' => 'www.wrong.com');
        $this->assertEndPointStatus(self::GET_METHOD, '/test/hostTest', self::STATUS_NOT_FOUND);
    }

    public function testCorrectHostCollection()
    {
        $this->clientOptions = array('HTTP_HOST' => 'www.test.com');
        $this->assertEndPointStatus(self::GET_METHOD, '/hostTest/test', self::STATUS_OK);
    }

    public function testWrongHostCollection()
    {
        $this->clientOptions = array('HTTP_HOST' => 'www.wrong.com');
        $this->assertEndPointStatus(self::GET_METHOD, '/hostTest/test', self::STATUS_NOT_FOUND);
    }
}
