<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\SourceCodeGenerators;

use XLite\Console\Command\SourceCodeGenerators\Renderer\TwigRenderer;

class Template
{
    /**
     * @var TwigRenderer
     */
    private $renderer;
    private $additionalParams = [];
    private $listChildren = [];
    private $bodyContentPlaceholder = '';

    public function __construct(TwigRenderer $renderer)
    {
        $this->renderer = $renderer;
    }


    /**
     * @param string $name
     * @param string $template
     *
     * @return string
     */
    public function generate($name, $template = 'base/template.twig')
    {
        return $this->renderer->render(
            $template,
            $this->mixWithAdditionalParams([
                'template' => [
                    'name'                   => $name,
                    'listChildren'           => $this->getListChildren(),
                    'bodyContentPlaceholder' => $this->getBodyContentPlaceholder(),
                ],
            ])
        );
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
     * @return array
     */
    public function getListChildren()
    {
        return $this->listChildren;
    }

    /**
     * @param array $listChildren
     */
    public function setListChildren($listChildren)
    {
        $this->listChildren = $listChildren;
    }

    /**
     * @param mixed  $listChild
     */
    public function addListChild($listChild)
    {
        $this->listChildren[] = $listChild;
    }

    protected function getBodyContentPlaceholder()
    {
        return $this->bodyContentPlaceholder;
    }

    /**
     * @param string $bodyContentPlaceholder
     */
    public function setBodyContentPlaceholder($bodyContentPlaceholder)
    {
        $this->bodyContentPlaceholder = $bodyContentPlaceholder;
    }
}
