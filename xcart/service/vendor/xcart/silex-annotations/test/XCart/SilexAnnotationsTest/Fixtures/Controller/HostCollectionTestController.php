<?php

namespace XCart\SilexAnnotationsTest\Fixtures\Controller;

use XCart\SilexAnnotations\Annotations\Router;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Router\Controller(prefix="hostTest")
 * @Router\Host("www.test.com")
 */
class HostCollectionTestController
{
    /**
     * @Router\Request(method="GET", uri="test")
     */
    public function testHost()
    {
        return new Response();
    }
} 