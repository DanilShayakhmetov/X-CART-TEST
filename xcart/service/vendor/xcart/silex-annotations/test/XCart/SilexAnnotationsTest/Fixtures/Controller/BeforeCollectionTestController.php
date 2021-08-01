<?php

namespace XCart\SilexAnnotationsTest\Fixtures\Controller;

use Symfony\Component\HttpFoundation\Response;
use XCart\SilexAnnotations\Annotations\Router;

/**
 * @Router\Controller(prefix="before")
 * @Router\Before("XCart\SilexAnnotationsTest\Controller\BeforeTestController::beforeCallback")
 */
class BeforeCollectionTestController
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
