<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\SourceCodeGenerators\Renderer;

use Includes\Decorator\Utils\CacheManager;
use Twig_Environment;
use XLite\Core\Templating\Twig\Loader\Filesystem;

class TwigRenderer
{
    public function __construct()
    {
        $this->loader = new Filesystem([
            LC_DIR_CLASSES . str_replace('/', LC_DS, 'XLite/Console/Command/SourceCodeGenerators/templates')
        ]);

        $this->twig = new Twig_Environment($this->loader, array(
            'cache'               => CacheManager::getCompileDir() . 'skins/',
            'debug'               => LC_DEVELOPER_MODE,
            'base_template_class' => '\\XLite\\Core\\Templating\\Twig\\Template',
        ));
    }

    public function render($templateName, $parameters)
    {
        $template = $this->twig->loadTemplate($templateName);

        return $template->render($parameters);
    }
}
