<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\SourceCodeGenerators\ItemsList;

use XLite\Console\Command\SourceCodeGenerators\PhpClass;
use XLite\Console\Command\SourceCodeGenerators\Utils;

class Controller
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
    public function generate($name, $namespace, $module = null)
    {
        $title = Utils::convertCamelToHumanReadable($name);

        $this->classGenerator->setParent('\XLite\Controller\Admin\AAdmin');
        $this->classGenerator->addMethod($this->getTitleMethod($title));

        return $this->classGenerator->generate(
            $name,
            $namespace
        );
    }

    /**
     * @param $title
     *
     * @return array
     */
    protected function getTitleMethod($title)
    {
        return [
            'name'        => 'getTitle',
            'description' => 'Get target title',
            'params'      => [],
            'body'        => 'return static::t(\'' . $title . '\');',
        ];
    }
}
