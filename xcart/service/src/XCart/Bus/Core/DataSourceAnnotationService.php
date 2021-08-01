<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core;

use ReflectionClass;
use XCart\Bus\Core\Annotations\DataSourceFilter;
use XCart\SilexAnnotations\AAnnotationService;

class DataSourceAnnotationService extends AAnnotationService
{
    /**
     * @param string $class
     *
     * @throws \Exception
     */
    protected function registerService($class)
    {
        $reflectionClass  = new ReflectionClass($class);
        $classAnnotations = $this->reader->getClassAnnotations($reflectionClass);

        foreach ($classAnnotations as $annotation) {
            if ($annotation instanceof DataSourceFilter) {
                $annotation->process($this->app, $reflectionClass);
            }
        }
    }
}
