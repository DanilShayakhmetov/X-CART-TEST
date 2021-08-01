<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core;

use ReflectionClass;
use XCart\Bus\Core\Annotations\RebuildScript;
use XCart\Bus\Core\Annotations\RebuildStep;
use XCart\SilexAnnotations\AAnnotationService;

class RebuildAnnotationService extends AAnnotationService
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
            if ($annotation instanceof RebuildScript
                || $annotation instanceof RebuildStep
            ) {
                $annotation->process($this->app, $reflectionClass);
            }
        }
    }
}
