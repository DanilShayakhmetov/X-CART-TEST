<?php

namespace XLite\Module\XCExample\FormDemo\View\Model;

class FormDemo extends \XLite\View\Model\AModel
{
    protected $schemaDefault = [
        'message' => [
            self::SCHEMA_CLASS      => 'XLite\View\FormField\Textarea\Simple',
            self::SCHEMA_LABEL      => 'Message',
            self::SCHEMA_REQUIRED   => true,            
        ],
    ];

    protected function getDefaultModelObject()
    {
        return null;
    }

    protected function getFormClass()
    {
        return 'XLite\Module\XCExample\FormDemo\View\Form\FormDemo';
    }
}