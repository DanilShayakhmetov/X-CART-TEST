<?php

namespace XCart\SilexAnnotationsTest\Fixtures\ServiceAnnotationSample;

use XCart\SilexAnnotations\Annotations\Service;

/**
 * @Service\Service(arguments={"service"="x_cart.silex_annotations_test.fixtures.service_annotation_sample.mapping_service"})
 */
class WithMappingService {
    public $service;
    public function __construct(MappedService $service)
    {
        $this->service = $service;
    }
}
