<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\SilexAnnotations\Annotations\Router;

use Silex\ControllerCollection;

/**
 * @Annotation
 * @Target("METHOD")
 */
class Route implements IRequest
{
    /**
     * @var Request[]
     */
    public $request;

    /**
     * @var Convert[]
     */
    public $convert;

    /**
     * @var Assert[]
     */
    public $assert;

    /**
     * @var RequireHttp[]
     */
    public $requireHttp;

    /**
     * @var RequireHttps[]
     */
    public $requireHttps;

    /**
     * @var Value[]
     */
    public $value;

    /**
     * @var Host[]
     */
    public $host;

    /**
     * @var Before[]
     */
    public $before;

    /**
     * @var After[]
     */
    public $after;

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        /** @var IRoute|IRoute[] $annotations */
        $annotations = $values['value'];
        $annotations = is_array($annotations) ? $annotations : [$annotations];

        foreach ($annotations as $annotation) {
            $classPath               = explode('\\', get_class($annotation));
            $propertyName            = lcfirst(array_pop($classPath));
            $this->{$propertyName}[] = $annotation;
        }
    }

    /**
     * Process annotations on a method to register it as a controller.
     *
     * @param ControllerCollection $controllerCollection
     * @param string               $handlerName
     */
    public function process(ControllerCollection $controllerCollection, $handlerName)
    {
        foreach ($this->request as $request) {
            $controller = $request->process($controllerCollection, $handlerName);
            foreach ((array) $this as $annotations) {
                if (is_array($annotations)) {
                    foreach ((array) $annotations as $annotation) {
                        if ($annotation instanceof IRoute) {
                            $annotation->process($controller);
                        }
                    }
                }
            }
        }
    }
}
