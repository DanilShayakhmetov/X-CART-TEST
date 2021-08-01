<?php

namespace XCart\SilexAnnotationsTest\Fixtures\Controller;

use XCart\SilexAnnotations\Annotations\Router;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Router\Controller(prefix="/testSecure")
 * @Router\Secure("ROLE_ADMIN")
 */
class SecureCollectionTestController
{
    /**
     * @Router\Request(method="GET", uri="/test")
     */
    public function testSecure()
    {
        return new Response();
    }
}
