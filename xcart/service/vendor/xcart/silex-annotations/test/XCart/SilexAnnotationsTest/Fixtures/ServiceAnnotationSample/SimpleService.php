<?php

namespace XCart\SilexAnnotationsTest\Fixtures\ServiceAnnotationSample;

use Silex\Application;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class SimpleService {
    public $app;
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
