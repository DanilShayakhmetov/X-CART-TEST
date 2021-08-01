<?php

namespace XCart\SilexAnnotationsTest\Fixtures\AnnotationsSample;

use ReflectionClass;
use Silex\Application;

/**
 * @Annotation
 * @Target("CLASS")
 */
class SomeAnnotation
{
    public $name;

    public function process(Application $app, ReflectionClass $reflectionClass)
    {}
}
