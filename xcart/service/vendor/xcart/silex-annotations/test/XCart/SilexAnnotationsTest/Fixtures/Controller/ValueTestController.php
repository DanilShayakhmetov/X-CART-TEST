<?php

namespace XCart\SilexAnnotationsTest\Fixtures\Controller;

use XCart\SilexAnnotations\Annotations\Router;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Router\Controller(prefix="/")
 */
class ValueTestController
{
    /**
     * @Router\Request(method="GET", uri="/{var}")
     * @Router\Value(variable="var", default="default")
     */
    public function testMethod($var)
    {
        return new Response($var);
    }
}
