<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

trait Select2Trait
{
    /**
     * Get value container class
     *
     * @return string
     */
    protected function getValueContainerClass()
    {
        $class = parent::getValueContainerClass();

        $class .= ' input-select2';

        return $class;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list                         = parent::getCommonFiles();
        $list[static::RESOURCE_JS][]  = 'select2/dist/js/select2.js';
        $list[static::RESOURCE_JS][]  = 'form_field/js/select2.generic.js';
        $list[static::RESOURCE_CSS][] = 'select2/dist/css/select2.min.css';

        return $list;
    }

    /**
     * This data will be accessible using JS core.getCommentedData() method.
     *
     * @return array
     */
    protected function getCommentedData()
    {
        return [
            'searching-lbl'   => static::t('Searching...'),
            'no-results-lbl'  => static::t('No results found.'),
            'enter-term-lbl'  => static::t('Enter a keyword to search.'),
            'placeholder-lbl' => $this->getPlaceholderLabel(),
            'more-lbl'   => static::t('Loading more results...'),
        ];
    }

    /**
     * @return mixed
     */
    protected function getPlaceholderLabel()
    {
        return static::t('Select an item');
    }
}