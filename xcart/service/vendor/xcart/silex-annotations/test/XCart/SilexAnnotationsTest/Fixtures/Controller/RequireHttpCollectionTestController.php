<?php

namespace XCart\SilexAnnotationsTest\Fixtures\Controller;

use XCart\SilexAnnotations\Annotations\Router;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Router\Controller(prefix="/requirehttp")
 * @Router\RequireHttp
*/
class RequireHttpCollectionTestController
{
    /**
     * @Router\Request(method="GET", uri="/test")
     */
    public function testRequireHttp()
    {
        return new Response();
    }
}