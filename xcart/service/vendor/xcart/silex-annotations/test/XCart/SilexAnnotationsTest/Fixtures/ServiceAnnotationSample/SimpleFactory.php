<?php

namespace XCart\SilexAnnotationsTest\Fixtures\ServiceAnnotationSample;

use Silex\Application;
use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Factory()
 */
class SimpleFactory {
    public $app;

    /**
     * @Service\Constructor()
     */
    public static function serviceConstructor(Application $app)
    {
        $instance = new self;
        $instance->app = $app;

        return $instance;
    }
}
