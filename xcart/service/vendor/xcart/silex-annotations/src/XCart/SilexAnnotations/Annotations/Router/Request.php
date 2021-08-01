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
 * @Target({"METHOD", "ANNOTATION"})
 */
class Request implements IRequest
{
    /**
     * @var string
     */
    public $method;

    /**
     * @var string
     */
    public $uri;

    /**
     * @param ControllerCollection $controllerCollection
     * @param string               $handlerName
     *
     * @return SilexController
     */
    public function process(ControllerCollection $controllerCollection, $handlerName)
    {
        $controller = $controllerCollection->match($this->uri, $handlerName);

        if ('MATCH' !== ($method = strtoupper($this->method))) {
            $controller = $controller->method($method);
        }

        return $controller;
    }
}
