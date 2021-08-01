<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations\Annotations\Router;

use Silex\ControllerCollection;

interface IRequest
{
    /**
     * @param ControllerCollection $controller
     * @param string               $handlerName
     */
    public function process(ControllerCollection $controller, $handlerName);
}
