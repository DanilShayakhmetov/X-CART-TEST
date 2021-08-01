<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\SourceCodeGenerators\Menu;

use XLite\Console\Command\SourceCodeGenerators\PhpClass;

class TopMenuDecorator
{
    /**
     * @var PhpClass
     */
    private $classGenerator;

    public function __construct(PhpClass $classGenerator)
    {
        $this->classGenerator = $classGenerator;
    }

    /**
     *
     * @param string $name
     * @param string $module
     *
     * @return string
     */
    public function generate($name, $namespace, $title, $menuPath, $target)
    {
        $this->classGenerator->setParent('\XLite\View\Menu\Customer\Top');
        $this->classGenerator->setInterfaces([ '\XLite\Base\IDecorator' ]);
        $this->classGenerator->setMethods(
            $this->getMethods(
                $title,
                $menuPath,
                $target
            )
        );

        return $this->classGenerator->generate(
            $name,
            $namespace,
            'menu/top_menu_decorator.twig'
        );
    }

    /**
     * @param $title
     *
     * @return array
     */
    protected function getMethods($title, $menuPath, $target)
    {
        return [
            [
                'name'        => 'defineItems',
                'params'      => [],
                'return'      => 'array',
                'menuPath'    => $menuPath,
                'target'      => $target,
                'title'       => $title,
            ],
        ];
    }
}
