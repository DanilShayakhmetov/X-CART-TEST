<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Bus\Core\Annotations;

use ReflectionClass;
use Silex\Application;
use XCart\Bus\Rebuild\Executor\ScriptFactory;

/**
 * @Annotation
 * @Target("CLASS")
 */
class RebuildStep
{
    public $script;

    public $weight;

    /**
     * @param Application      $app
     * @param \ReflectionClass $reflectionClass
     */
    public function process(Application $app, ReflectionClass $reflectionClass)
    {
        $app[ScriptFactory::class]->addStep(
            $this->script,
            $reflectionClass->getName(),
            $this->weight
        );
    }
}
