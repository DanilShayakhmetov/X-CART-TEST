<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\SourceCodeGenerators;

use XLite\Console\Command\SourceCodeGenerators\Renderer\TwigRenderer;

class PhpClass
{
    /**
     * @var TwigRenderer
     */
    private $renderer;
    private $parent;
    private $interfaces;
    private $traits;
    private $methods = [];
    private $additionalParams = [];

    public function __construct(TwigRenderer $renderer)
    {
        $this->renderer = $renderer;
    }


    /**
     * @param string $name
     * @param string $namespace
     * @param string $template
     *
     * @return string
     */
    public function generate($name, $namespace, $template = 'base/php-class.twig')
    {
        return $this->renderer->render(
            $template,
            $this->mixWithAdditionalParams([
                'class' => [
                    'name'          => $name,
                    'namespace'     => $namespace,
                    'parent'        => $this->parent,
                    'methods'       => $this->getMethods(),
                    'interfaces'    => $this->interfaces,
                    'traits'        => $this->traits
                ],
            ])
        );
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @param mixed $interfaces
     */
    public function setInterfaces($interfaces)
    {
        $this->interfaces = $interfaces;
    }

    /**
     * @param mixed $interface
     */
    public function addInterface($interface)
    {
        $this->interfaces[] = $interface;
    }

    /**
     * @return array
     */
    protected function getMethods()
    {
        return $this->methods;
    }

    public function clearMethods()
    {
        $this->methods = [];
    }

    /**
     * @param array $method
     */
    public function addMethod($method)
    {
        $this->methods[] = $method;
    }

    /**
     * @param array $methods
     */
    public function setMethods($methods)
    {
        $this->methods = $methods;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    protected function mixWithAdditionalParams(array $params)
    {
        return array_merge(
            $params,
            $this->additionalParams
        );
    }

    /**
     * @return array
     */
    public function getAdditionalParams()
    {
        return $this->additionalParams;
    }

    /**
     * @param array $additionalParams
     */
    public function setAdditionalParams($additionalParams)
    {
        $this->additionalParams = $additionalParams;
    }

    /**
     * @param string $name
     * @param mixed  $additionalParam
     */
    public function addAdditionalParam($name, $additionalParam)
    {
        $this->additionalParams[$name] = $additionalParam;
    }

    /**
     * @return mixed
     */
    public function getTraits()
    {
        return $this->traits;
    }

    /**
     * @param mixed $traits
     */
    public function setTraits($traits)
    {
        $this->traits = $traits;
    }

    /**
     * @param mixed $trait
     */
    public function addTrait($trait)
    {
        $this->traits[] = $trait;
    }
}
