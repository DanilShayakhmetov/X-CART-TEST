<?php

namespace XCart\SilexAnnotationsTest\Fixtures\ServiceAnnotationSample;

use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service()
 */
class WithoutMappingService {
    public $service;
    public function __construct(MappedService $service)
    {
        $this->service = $service;
    }
}
