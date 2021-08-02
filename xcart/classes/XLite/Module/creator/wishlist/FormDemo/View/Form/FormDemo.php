<?php

namespace XLite\Module\XCExample\FormDemo\View\Form;

class FormDemo extends \XLite\View\Form\AForm
{
    protected function getDefaultTarget()
    {
        return 'example_form_demo';
    }

    protected function getDefaultAction()
    {
        return 'send';
    }
}