<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating\Twig;

use Twig_Environment;
use XLite\Core\Layout;
use XLite\Core\Translation;

/**
 * Custom twig functions
 *
 * TODO: Move widget instantiation logic from AView to a separate WidgetFactory
 */
class Functions
{
    protected $layout;

    public function __construct()
    {
        $this->layout = Layout::getInstance();
    }

    public function widget(Twig_Environment $env, $context, array $arguments = [])
    {
        $nextPositionalArgument = 0;

        $class = null;

        if (isset($arguments[$nextPositionalArgument]) && is_string($arguments[$nextPositionalArgument])) {
            $class = $arguments[$nextPositionalArgument];
            unset($arguments[$nextPositionalArgument]);
            $nextPositionalArgument++;
        }

        /** @var \XLite\View\AView $widget */
        $widget = $env->getGlobals()['this'];

        return $widget->getWidget(
            isset($arguments[$nextPositionalArgument]) ? $arguments[$nextPositionalArgument] : $arguments,
            $class
        );
    }

    public function widget_list(Twig_Environment $env, $context, array $arguments = [])
    {
        $type = isset($arguments['type']) ? strtolower($arguments['type']) : null;

        unset($arguments['type']);

        $name = $arguments[0];

        unset($arguments[0]);

        if (isset($arguments[1])) {
            // Instantiate widget list with parameters passed in the second positional argument ($arguments[1])

            $params = $arguments[1];
        } else {
            $params = $arguments;
        }

        /** @var \XLite\View\AView $widget */
        $widget = $env->getGlobals()['this'];
        if ($type === 'inherited') {
            $widget->displayInheritedViewListContent($name, $params);
        } elseif ($type === 'nested') {
            $widget->displayNestedViewListContent($name, $params);
        } else {
            $widget->displayViewListContent($name, $params);
        }
    }

    public function t($name, array $arguments = [], $code = null, $type = null)
    {
        return Translation::lbl($name, $arguments, $code, $type);
    }

    public function svg(Twig_Environment $env, $context, $path, $interface = null)
    {
        return $env->getGlobals()['this']->getSVGImage($path, $interface);
    }

    public function url(Twig_Environment $env, $context, $target = '', $action = '', array $params = [], $forceCuFlag = null)
    {
        return $env->getGlobals()['this']->buildURL($target, $action, $params, $forceCuFlag);
    }

    public function asset($path)
    {
        return $this->layout->getResourceWebPath($path, Layout::WEB_PATH_OUTPUT_URL)
            ?: $this->layout->prepareSkinURL($path, Layout::WEB_PATH_OUTPUT_URL);
    }
}
