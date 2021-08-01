<?php

namespace XCart\SilexAnnotationsTest\Fixtures\Controller;

use XCart\SilexAnnotations\Annotations\Router;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Router\Controller
 * @Router\Before("XCart\SilexAnnotations\Test\Annotations\BeforeTestController::beforeCallback")
 */
class TestControllerWithNoPrefix
{
    /**
     * @Router\Request(method="GET", uri="/test")
     */
    public function testMethod($var)
    {
        return new Response($var);
    }

    public static function beforeCallback()
    {
        throw new \Exception('before callback');
    }
}
