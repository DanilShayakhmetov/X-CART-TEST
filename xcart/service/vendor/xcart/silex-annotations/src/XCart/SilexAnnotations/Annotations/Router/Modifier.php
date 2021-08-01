<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations\Annotations\Router;

use RuntimeException;
use Silex\Controller as SilexController;
use Silex\ControllerCollection;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Modifier implements IRoute
{
    /**
     * @var string
     */
    public $method;

    /**
     * @var array
     */
    public $args;

    /**
     * @param SilexController|ControllerCollection $controller
     *
     * @throws \RuntimeException
     */
    public function process($controller)
    {
        try {
            call_user_func_array([$controller, $this->method], $this->args ?: []);

        } catch (\BadMethodCallException $ex) {
            throw new RuntimeException("Modifier: [$this->method] does not exist.");
        }
    }
}
