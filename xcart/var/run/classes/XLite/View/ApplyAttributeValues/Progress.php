<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ApplyAttributeValues;


class Progress extends \XLite\View\AView
{
    use \XLite\View\EventTaskProgressProviderTrait;

    /**
     * Returns processing unit
     * @return mixed
     */
    protected function getProcessor()
    {
        return \XLite\Logic\ApplyAttributeValues\Generator::getInstance();
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/controller.js';

        return $list;
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'apply_attribute_values';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/progress.twig';
    }
}