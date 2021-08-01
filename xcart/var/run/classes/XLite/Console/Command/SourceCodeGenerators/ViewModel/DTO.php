<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\SourceCodeGenerators\ViewModel;

use XLite\Console\Command\SourceCodeGenerators\PhpClass;
use XLite\Core\Converter;

class DTO
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
    public function generate($name, $namespace, $fields)
    {
        $this->classGenerator->setParent('\XLite\Model\DTO\Base\ADTO');
        $this->classGenerator->setMethods($this->getMethods());
        $this->classGenerator->addAdditionalParam('fields', $this->getFields($fields));

        return $this->classGenerator->generate(
            $name,
            $namespace,
            'view_model/dto.twig'
        );
    }

    /**
     * @return array
     */
    protected function getMethods()
    {
        $methods = [];

        $methods[] = [
            'name'        => 'init',
            'description' => '',
            'params'      => [
                [
                    'name' => 'object',
                    'required' => true
                ]
            ],
        ];
        $methods[] = [
            'name'        => 'populateTo',
            'description' => 'Action',
            'return'      => 'mixed',
            'params'      => [
                [
                    'name' => 'object',
                    'required' => true
                ],
                [
                    'name'     => 'rawData',
                    'required' => false
                ]
            ],
        ];

        return $methods;
    }

    protected function getFields($fields)
    {
        return array_map(function($field) {
            return [
                'code'      => $field['name'],
                'getter'    => 'get' . Converter::convertToCamelCase($field['name']),
                'setter'    => 'set' . Converter::convertToCamelCase($field['name']),
            ];
        }, $fields);
    }

    // {{{ Getters, Setters

    // }}}
}
