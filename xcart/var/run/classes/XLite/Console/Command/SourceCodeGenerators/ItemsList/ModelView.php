<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\SourceCodeGenerators\ItemsList;

use XLite\Console\Command\SourceCodeGenerators\PhpClass;
use XLite\Console\Command\SourceCodeGenerators\Utils;

class ModelView
{
    /**
     * @var PhpClass
     */
    private $classGenerator;

    private $isSwitchable = false;
    private $isRemovable = false;
    private $isSortable = false;
    private $isWrappedWithForm = true;
    private $stylePath;
    private $canCreate = false;
    private $canEdit = false;
    private $createTarget;
    private $editTarget;
    private $inlineCreation = false;

    public function __construct(PhpClass $classGenerator)
    {
        $this->classGenerator = $classGenerator;
    }

    /**
     * @return string
     */
    public function generate($name, $namespace, $modelName, $module, $target, $fields)
    {
        $this->classGenerator->setParent('\XLite\View\ItemsList\Model\Table');
        $this->classGenerator->setMethods($this->getMethods($modelName, $target, $target, $fields));

        $this->classGenerator->addAdditionalParam('columns', $this->getColumns($fields, $modelName, $target));
        if ($this->getCanCreate()) {
            $this->classGenerator->addAdditionalParam('createUrl', $target);
        }

        return $this->classGenerator->generate(
            $name,
            $namespace,
            'items_list/model_view.twig'
        );
    }

    /**
     * @param string $modelName
     * @param string $target
     * @param string $formTarget
     *
     * @return array
     */
    protected function getMethods($modelName, $target, $formTarget, $fields)
    {
        $methods = [
            [
                'name'        => 'getAllowedTargets',
                'description' => 'Return list of allowed targets',
                'params'      => [],
                'static'      => true,
                'target'      => $target
            ],
            [
                'name'        => 'defineColumns',
                'description' => 'Define columns structure',
                'params'      => [],
            ],
            [
                'name'        => 'defineRepositoryName',
                'description' => 'Define repository name',
                'params'      => [],
                'body'          => 'return \''.$modelName.'\';',
            ],
        ];

        if ($this->isWrappedWithForm()) {
            $methods = array_merge(
                $methods,
                [
                    [
                        'name'        => 'wrapWithFormByDefault',
                        'description' => '',
                        'params'      => [],
                        'body'        => 'return true;'
                    ],
                    [
                        'name'        => 'getFormTarget',
                        'description' => 'Get wrapper form target',
                        'params'      => [],
                        'body'        => 'return \''. $formTarget .'\';'
                    ],
                ]
            );
        }

        if ($this->getStylePath()) {
            $path = $this->getStylePath();
            $methodBody = <<<CODE
\$list = parent::getCSSFiles();

    \$list[] = '$path';

    return \$list;
CODE;

            $methods[] = [
                'name'        => 'getCSSFiles',
                'description' => 'Register CSS files',
                'return'      => 'array',
                'params'      => [],
                'body'        => $methodBody
            ];
        }

        if ($this->isRemovable()) {
            $methods[] = [
                'name'        => 'isRemoved',
                'description' => 'Mark list as removable',
                'params'      => [],
                'body'        => 'return true;'
            ];
        }

        if ($this->isSwitchable()) {
            $methods[] = [
                'name'        => 'isSwitchable',
                'description' => 'Mark list as switchable (enable / disable)',
                'params'      => [],
                'body'        => 'return true;'
            ];
        }

        if ($this->isSortable()) {
            $methods[] = [
                'name'        => 'getSortableType',
                'description' => 'Mark list as sortable',
                'params'      => [],
                'body'        => 'return static::SORT_TYPE_MOVE;'
            ];
        }

        if ($this->getCanCreate()) {
            $methods[] = [
                'name'        => 'isCreation',
                'description' => 'Creation button position',
                'return'      => 'integer',
                'params'      => [],
                'body'        => 'return static::CREATE_INLINE_TOP;'
            ];

            if ($this->getInlineCreation()) {
                $methods[] = [
                    'name'        => 'isInlineCreation',
                    'description' => 'Inline creation mechanism position',
                    'return'      => 'integer',
                    'params'      => [],
                    'body'        => 'return static::CREATE_INLINE_TOP;'
                ];
            } else {
                $createTarget = $this->getCreateTarget()
                    ?: $target;
                $methods[] = [
                    'name'        => 'getCreateURL',
                    'description' => 'Get create entity URL',
                    'return'      => 'string',
                    'params'      => [],
                    'body'        => "return \XLite\Core\Converter::buildUrl('$createTarget');"
                ];
            }
        }

        $searchFields = $this->getSearchFields($fields);

        if ($searchFields) {
            $methods[] = [
                'name'        => 'getSearchPanelClass',
                'description' => 'Get search panel widget class',
                'return'      => 'string',
                'params'      => [],
                'body'        => 'return \'XLite\View\SearchPanel\SimpleSearchPanel\';'
            ];
            $methods[] = [
                'name'        => 'getSearchFormOptions',
                'description' => 'Get search form options',
                'return'      => 'array',
                'params'      => [],
                'body'        => "return [ 'target' => '$target' ];"
            ];
            $methods[] = [
                'name'        => 'getSearchCaseProcessor',
                'description' => 'Get search case (aggregated search conditions) processor',
                'return'      => '\XLite\View\ItemsList\ISearchCaseProvider',
                'params'      => [],
                'static'       => true,
            ];
            $methods[] = [
                'name'         => 'getSearchParams',
                'description'  => 'Return search parameters.',
                'return'       => 'array',
                'params'       => [],
                'static'       => true,
                'searchFields' => $searchFields,
            ];
        }

        $orderByOptions = $this->getOrderByOptions($fields, $modelName);
        if ($orderByOptions) {
            $methods[] = [
                'name'         => '__construct',
                'description'  => 'Initialize',
                'return'       => 'array',
                'params'       => [
                    [
                        'type'    => 'array',
                        'name'    => 'params',
                        'default' => '[]'
                    ]
                ],
                'sortOptions' => $orderByOptions,
            ];
        }

        return $methods;
    }

    /**
     * @param $fields
     *
     * @return array
     */
    protected function getSearchFields($fields)
    {
        $fields = array_filter($fields, function($field) {
            return $field['isSearch'];
        });

        return array_map(function($field) {
            return [
                'name'       => $field['name'],
                'searchName' => $field['name'] . '_search',
                'humanName'  => Utils::convertCamelToHumanReadable($field['name']),
            ];
        }, $fields);
    }

    /**
     * @param array  $fields
     * @param string $modelName
     * @param string $target
     *
     * @return array
     */
    protected function getColumns($fields, $modelName, $target)
    {
        $result = [];
        $repo = \XLite\Core\Database::getRepo($modelName);

        foreach ($fields as $field) {
            // TODO Make editable
            $class = 'XLite\View\FormField\Inline\Input\Text';
            $column = [
                'code'      => $field['name'],
                'name'      => Utils::convertCamelToHumanReadable($field['name']),
                'viewClass' => $class,
                'sort'      => $repo->getDefaultAlias() . '.' . $field['name'],
            ];

            if ($field['isEdit']) {
                $column['link'] = $this->getEditTarget();
                unset($column['viewClass']);
            }

            $result[] = $column;
        }

        return $result;
    }

    /**
     * @param $fields
     * @param $modelName
     *
     * @return array
     */
    protected function getOrderByOptions($fields, $modelName)
    {
        $result = [];
        $repo = \XLite\Core\Database::getRepo($modelName);

        foreach ($fields as $field) {
            $column = [
                'code'      => $repo->getDefaultAlias() . '.' . $field['name'],
                'name'      => Utils::convertCamelToHumanReadable($field['name']),
            ];

            $result[] = $column;
        }

        return $result;
    }

    /**
     * TODO Make editable
     */
    public static function getTypes()
    {
        return [
            'integer'  => 'XLite\View\FormField\Inline\Input\Text\Integer',
            'uinteger' => 'XLite\View\FormField\Inline\Input\Text\Integer',
            'float'    => 'XLite\View\FormField\Inline\Input\Text\FloatInput',
            'money'    => 'XLite\View\FormField\Inline\Input\Text\Price',
        ];
    }

    // {{{ Getters, Setters

    /**
     * @return bool
     */
    public function isWrappedWithForm()
    {
        return $this->isWrappedWithForm;
    }

    /**
     * @param bool $isWrappedWithForm
     */
    public function setIsWrappedWithForm($isWrappedWithForm)
    {
        $this->isWrappedWithForm = $isWrappedWithForm;
    }

    /**
     * @return bool
     */
    public function isSwitchable()
    {
        return $this->isSwitchable;
    }

    /**
     * @param bool $isSwitchable
     */
    public function setIsSwitchable($isSwitchable)
    {
        $this->isSwitchable = $isSwitchable;
    }

    /**
     * @return bool
     */
    public function isRemovable()
    {
        return $this->isRemovable;
    }

    /**
     * @param bool $isRemovable
     */
    public function setIsRemovable($isRemovable)
    {
        $this->isRemovable = $isRemovable;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return $this->isSortable;
    }

    /**
     * @param bool $isSortable
     */
    public function setIsSortable($isSortable)
    {
        $this->isSortable = $isSortable;
    }

    /**
     * @return string
     */
    public function getStylePath()
    {
        return $this->stylePath;
    }

    /**
     * @param string $stylePath
     */
    public function setStylePath($stylePath)
    {
        $this->stylePath = $stylePath;
    }

    /**
     * @return mixed
     */
    public function getCanEdit()
    {
        return $this->canEdit;
    }

    /**
     * @param mixed $canEdit
     */
    public function setCanEdit($canEdit)
    {
        $this->canEdit = $canEdit;
    }

    /**
     * @return mixed
     */
    public function getInlineCreation()
    {
        return $this->inlineCreation;
    }

    /**
     * @param mixed $inlineCreation
     */
    public function setInlineCreation($inlineCreation)
    {
        $this->inlineCreation = $inlineCreation;
    }

    /**
     * @return mixed
     */
    public function getCanCreate()
    {
        return $this->canCreate;
    }

    /**
     * @param mixed $canCreate
     */
    public function setCanCreate($canCreate)
    {
        $this->canCreate = $canCreate;
    }

    /**
     * @return mixed
     */
    public function getCreateTarget()
    {
        return $this->createTarget;
    }

    /**
     * @param mixed $createTarget
     */
    public function setCreateTarget($createTarget)
    {
        $this->createTarget = $createTarget;
    }

    /**
     * @return mixed
     */
    public function getEditTarget()
    {
        return $this->editTarget;
    }

    /**
     * @param mixed $editTarget
     */
    public function setEditTarget($editTarget)
    {
        $this->editTarget = $editTarget;
    }

    // }}}
}
