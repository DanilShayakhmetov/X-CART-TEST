<?php

namespace XCart\SilexAnnotationsTest\Fixtures\Controller;

use Symfony\Component\HttpFoundation\Response;
use XCart\SilexAnnotations\Annotations\Router;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 * @Router\Controller(prefix="/")
 */
class ServiceTestController
{
    /**
     * @Router\Request(method="GET", uri="/service")
     */
    public function testMethod()
    {
        return new Response(self::class);
    }
}
