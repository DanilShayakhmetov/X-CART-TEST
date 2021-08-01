<?php

namespace XCart\SilexAnnotationsTest\Fixtures\AnnotationsSample;

use ReflectionClass;
use XCart\SilexAnnotations\AAnnotationService;

class SomeAnnotationService extends AAnnotationService
{
    /**
     * @param string $class
     *
     * @throws \ReflectionException
     */
    protected function registerService($class)
    {
        $reflectionClass  = new ReflectionClass($class);
        $classAnnotations = $this->reader->getClassAnnotations($reflectionClass);

        foreach ($classAnnotations as $annotation) {
            if ($annotation instanceof SomeAnnotation) {
                $annotation->process($this->app, $reflectionClass);
            }
        }
    }
}
