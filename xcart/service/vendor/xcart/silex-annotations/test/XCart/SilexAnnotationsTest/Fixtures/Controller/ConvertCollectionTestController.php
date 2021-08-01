<?php

namespace XCart\SilexAnnotationsTest\Fixtures\Controller;

use XCart\SilexAnnotations\Annotations\Router;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Router\Controller(prefix="/convert")
 * @Router\Convert(variable="var", callback="XCart\SilexAnnotationsTest\Fixtures\Controller\ConvertCollectionTestController::convert")
 */
class ConvertCollectionTestController
{
    /**
     * @Router\Request(method="GET", uri="/test/{var}")
     */
    public function testMethod($var)
    {
        return new Response($var);
    }

    public static function convert($var)
    {
        return $var + 5;
    }
}