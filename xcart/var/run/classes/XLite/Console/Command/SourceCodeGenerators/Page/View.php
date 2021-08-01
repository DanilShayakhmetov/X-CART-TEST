<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Console\Command\SourceCodeGenerators\Page;

use XLite\Console\Command\SourceCodeGenerators\PhpClass;

class View
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
    public function generate($name, $namespace, $zone, $target, $pageTemplate)
    {
        $this->classGenerator->setParent(
            '\XLite\View\AView'
        );
        $list = $zone === 'admin'
            ? 'admin.center'
            : 'center';
        $this->classGenerator->addAdditionalParam('listChild', [
           'list' => $list,
           'zone' => $zone,
        ]);
        $this->classGenerator->setMethods($this->getMethods($target, $pageTemplate));

        return $this->classGenerator->generate(
            $name,
            $namespace,
            'page/view.twig'
        );
    }

    protected function getMethods($target, $pageTemplate)
    {
        return [
            [
                'name'        => 'getAllowedTargets',
                'description' => 'Return list of allowed targets',
                'params'      => [],
                'return'      => 'array',
                'static'      => true,
                'body'        => "return array_merge(parent::getAllowedTargets(), array('$target'));",
            ],
            [
                'name'        => 'getDefaultTemplate',
                'description' => 'Return widget default template',
                'params'      => [],
                'return'      => 'string',
                'body'        => "return '$pageTemplate';",
            ],
        ];
    }
    public function mapZoneToParentController($zone)
    {
        return [
            'admin' => '\XLite\Controller\Admin\AAdmin',
            'customer' => '\XLite\Controller\Customer\ACustomer',
        ];
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
