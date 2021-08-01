<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Product classes selector
 */
class ProductClasses extends \XLite\View\FormField\Select\Multiple
{
    use Select2Trait {
        getValueContainerClass as getSelect2ValueContainerClass;
    }

    /**
     * @return string
     */
    protected function getValueContainerClass()
    {
        $class = $this->getSelect2ValueContainerClass();

        $class .= ' input-product-classes-select2';

        return $class;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = $this->getDir() . '/select/product_classes.js';

        return $list;
    }

    /**
     * @return mixed
     */
    protected function getPlaceholderLabel()
    {
        return static::t('All');
    }

    /**
     * Get product classes list
     *
     * @return array
     */
    protected function getProductClassesList()
    {
        $list = array();
        foreach (\XLite\Core\Database::getRepo('\XLite\Model\ProductClass')->search() as $e) {
            $list[$e->getId()] = $e->getName();
        }

        return $list;
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array('0' => 'All') + $this->getProductClassesList();
    }
}
