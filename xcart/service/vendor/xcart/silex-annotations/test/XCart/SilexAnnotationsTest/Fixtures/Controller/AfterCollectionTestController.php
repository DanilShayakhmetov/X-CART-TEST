<?php

namespace XCart\SilexAnnotationsTest\Fixtures\Controller;

use XCart\SilexAnnotations\Annotations\Router;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Router\Controller(prefix="/after")
 * @Router\After("XCart\SilexAnnotationsTest\Controller\AfterTestController::afterCallback")
 */
class AfterCollectionTestController
{
    /**
     * @Router\Request(method="GET", uri="/test")
     */
    public function testMethod($var)
    {
        return new Response($var);
    }

    public static function afterCallback()
    {
        throw new Exception('after callback');
    }
}