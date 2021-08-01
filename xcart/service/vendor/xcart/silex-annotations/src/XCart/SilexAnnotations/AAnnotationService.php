<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations;

use Doctrine\Common\Annotations\Reader;
use Silex\Application;

abstract class AAnnotationService
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @param string $class
     */
    abstract protected function registerService($class);

    /**
     * @param Application $app
     * @param Reader      $reader
     */
    public function __construct(Application $app, Reader $reader)
    {
        $this->app    = $app;
        $this->reader = $reader;
    }

    /**
     * @param string[] $classes
     */
    public function register($classes)
    {
        foreach ($classes as $class) {
            $this->registerService($class);
        }
    }
}
