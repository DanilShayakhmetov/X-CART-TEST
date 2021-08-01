<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;


abstract class WithInsertLink extends \XLite\View\FormField\Input\Text
{
    /**
     * @return string
     */
    abstract protected function getInsertValue();

    /**
     * @return string
     */
    abstract protected function getInsertLabel();

    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'form_field/text/with_insert_link.js',
        ]);
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'form_field/text/with_insert_link.less',
        ]);
    }

    protected function getFieldTemplate()
    {
        return 'text/with_insert_link.twig';
    }

    protected function getCommentedData()
    {
        return parent::getCommentedData() + [
                'insert_value' => $this->getInsertValue(),
            ];
    }

    public function getWrapperClass()
    {
        return parent::getWrapperClass() . ' with-insert-link';
    }
}