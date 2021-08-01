<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\SourceCodeGenerators\ViewModel;

use XLite\Console\Command\SourceCodeGenerators\PhpClass;
use XLite\Console\Command\SourceCodeGenerators\Utils;
use XLite\Core\Converter;

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
    public function generate($name, $namespace, $modelClass, $dtoClass, $viewModelClass)
    {
        $title = Utils::convertCamelToHumanReadable($name);

        $this->classGenerator->setParent('\XLite\Controller\Admin\AAdmin');
        $this->classGenerator->addTrait('\XLite\Controller\Features\FormModelControllerTrait');
        $this->classGenerator->setMethods(
            $this->getMethods(
                $title,
                $dtoClass,
                $viewModelClass,
                Converter::convertFromCamelCase($name)
            )
        );

        $this->classGenerator->addAdditionalParam('modelClass', $modelClass);
        $this->classGenerator->addAdditionalParam('idName', \XLite\Core\Database::getRepo($modelClass)->getPrimaryKeyField());

        return $this->classGenerator->generate(
            $name,
            $namespace,
            'view_model/controller.twig'
        );
    }

    /**
     * @param $title
     *
     * @return array
     */
    protected function getMethods($title, $dtoClass, $viewModelClass, $returnUrl)
    {
        $successText = 'The fields has been updated';
        $failText = 'The fields has not been updated';

        return [
            [
                'name'        => 'getTitle',
                'description' => 'Get target title',
                'params'      => [],
                'body'        => 'return static::t(\'' . $title . '\');',
            ],
            [
                'name'        => 'getFormModelObject',
                'description' => 'Returns object to get initial data and populate submitted data to',
                'params'      => [],
                'return'      => '\XLite\Model\DTO\Base\ADTO',
                'body'        => "return new $dtoClass(\$this->getModelObject());",
            ],
            [
                'name'        => 'getModelObject',
                'description' => 'Returns model object',
                'params'      => [],
                'return'      => '\XLite\Model\AEntity',
            ],
            [
                'name'           => 'doActionUpdate',
                'description'    => 'Update model',
                'params'         => [],
                'formModelClass' => $viewModelClass,
                'successText'    => $successText,
                'failText'       => $failText,
                'returnUrl'      => $returnUrl,
            ],
        ];
    }
}
