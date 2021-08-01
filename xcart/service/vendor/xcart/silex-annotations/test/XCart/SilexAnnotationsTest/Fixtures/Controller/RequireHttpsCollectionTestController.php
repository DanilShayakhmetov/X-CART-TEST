<?php

namespace XCart\SilexAnnotationsTest\Fixtures\Controller;

use XCart\SilexAnnotations\Annotations\Router;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Router\Controller(prefix="/requirehttps")
 * @Router\RequireHttps
*/
class RequireHttpsCollectionTestController
{
    /**
     * @Router\Request(method="GET", uri="/test")
     */
    public function testRequiresHttp()
    {
        return new Response();
    }
}