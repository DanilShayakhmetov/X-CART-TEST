<?php

namespace XCart\SilexAnnotationsTest\Fixtures\Controller;

use XCart\SilexAnnotations\Annotations\Router;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Router\Controller(prefix="assert")
 * @Router\Assert(variable="var", regex="\d+")
 */
class AssertCollectionTestController
{
    /**
     * @Router\Request(method="GET", uri="test/{var}")
     */
    public function testMethod($var)
    {
        return new Response($var);
    }
}
