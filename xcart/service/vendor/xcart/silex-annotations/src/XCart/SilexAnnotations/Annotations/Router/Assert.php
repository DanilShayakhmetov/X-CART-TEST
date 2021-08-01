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
class Assert implements IRoute
{
    /**
     * @var string
     */
    public $variable;

    /**
     * @var string
     */
    public $regex;

    /**
     * @param SilexController|ControllerCollection $controller
     */
    public function process($controller)
    {
        $controller->assert($this->variable, $this->regex);
    }
}
