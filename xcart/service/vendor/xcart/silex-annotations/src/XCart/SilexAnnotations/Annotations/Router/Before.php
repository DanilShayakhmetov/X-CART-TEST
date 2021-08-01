<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations\Annotations\Router;

use Silex\Controller as SilexController;
use Silex\ControllerCollection;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "ANNOTATION"})
 */
class Before implements IRoute
{
    /**
     * @var string
     */
    public $callback;

    /**
     * @param SilexController|ControllerCollection $controller
     */
    public function process($controller)
    {
        $controller->before($this->callback);
    }
}
