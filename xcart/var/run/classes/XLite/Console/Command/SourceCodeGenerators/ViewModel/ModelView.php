<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\SourceCodeGenerators\ViewModel;

use XLite\Console\Command\SourceCodeGenerators\PhpClass;

class ModelView
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
     * @return string
     */
    public function generate($name, $namespace, $modelClass, $target, $fields)
    {
        $this->classGenerator->setParent('\XLite\View\FormModel\AFormModel');
        $this->classGenerator->setMethods(
            $this->getMethods($target, $fields)
        );
        $this->classGenerator->addAdditionalParam('idName', \XLite\Core\Database::getRepo($modelClass)->getPrimaryKeyField());

        return $this->classGenerator->generate(
            $name,
            $namespace,
            'view_model/view.twig'
        );
    }

    /**
     * @param string $target
     *
     * @param        $fields
     *
     * @return array
     */
    protected function getMethods($target, $fields)
    {
        $methods = [];

        $methods[] = [
            'name'         => 'getTarget',
            'description'  => 'Target',
            'return'       => 'string',
            'params'       => [],
            'body' => "return '$target';",
        ];
        $methods[] = [
            'name'         => 'getAction',
            'description'  => 'Action',
            'return'       => 'string',
            'params'       => [],
            'body' => "return 'update';",
        ];
        $methods[] = [
            'name'         => 'getActionParams',
            'description'  => '',
            'return'       => 'string',
            'params'       => [],
            'body' => "return 'update';",
        ];

        $methods[] = [
            'name'         => 'getAllowedTargets',
            'description'  => 'Return list of targets allowed for this widget',
            'return'       => 'array',
            'params'       => [],
            'static'       => true,
            'target'       => $target,
        ];

        $methods[] = [
            'name'         => 'defineFields',
            'description'  => '',
            'return'       => 'array',
            'params'       => [],
            'fields'       => $this->getFields($fields),
        ];

        return $methods;
    }

    protected function getFields($fields)
    {
        $i = 0;

        return array_map(function($field) use ($i) {
            $i += 100;

            return [
                'code'      => $field['name'],
                'humanName' => $field['humanName'],
                'position'  => $i,
            ];
        }, $fields);
    }

    // {{{ Getters, Setters

    // }}}
}
