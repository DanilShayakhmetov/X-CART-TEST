<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to export page
 */
class Export extends \XLite\View\Tabs\ATabs
{
    /**
     * Widget parameter names
     */
    const PARAM_PRESELECT = 'preselect';

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = [
            'new' => [
                'weight'   => 100,
                'title'    => static::t('New export'),
                'template' => 'export/parts/begin.new_export.twig'
            ]
        ];

        if ($this->downloadFilesAvailable()) {
            $tabs['last'] = [
                'weight'   => 200,
                'title'    => static::t('Last exported'),
                'template' => 'export/parts/begin.last_export.twig'
            ];
        }

        return $tabs;
    }


    /**
     * Returns tab URL
     *
     * @param string $target Tab target
     *
     * @return string
     */
    protected function buildTabURL($target)
    {
        return $this->buildURL(
            'export',
            '',
            [
                'page'       => $target
            ]
        );
    }


    /**
     * Returns the current target
     *
     * @return string
     */
    protected function getCurrentTarget()
    {
        return \XLite\Core\Request::getInstance()->page ?: 'new';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_PRESELECT => new \XLite\Model\WidgetParam\TypeString('Preselected class', 'XLite\Logic\Export\Step\Products'),
        );
    }

    /**
     * Define sections list
     *
     * @return string[]
     */
    protected function defineSections()
    {
        return array(
            'XLite\Logic\Export\Step\Products'                               => 'Products',
            'XLite\Logic\Export\Step\Attributes'                             => 'Classes & Attributes',
            'XLite\Logic\Export\Step\AttributeValues\AttributeValueCheckbox' => 'Product attributes values',
            'XLite\Logic\Export\Step\Orders'                                 => 'Orders',
            'XLite\Logic\Export\Step\Categories'                             => 'Categories',
            'XLite\Logic\Export\Step\Users'                                  => 'Customers',
        );
    }

    /**
     * Return sections list
     *
     * @return string[]
     */
    protected function getSections()
    {
        return $this->defineSections();
    }

    /**
     * Check section is selected or not
     *
     * @param string $class Class
     *
     * @return boolean
     */
    protected function isSectionSelected($class)
    {
        return $this->getParam(static::PARAM_PRESELECT) == $class && !$this->isSectionDisabled($class)
            && !$this->isSectionDisabled($class);
    }

    /**
     * Check section is disabled or not
     *
     * @param string $class Class
     *
     * @return boolean
     */
    protected function isSectionDisabled($class)
    {
        $found = false;

        $classes = array();

        $classes[] = $class;

        if ('XLite\Logic\Export\Step\AttributeValues\AttributeValueCheckbox' == $class) {
            $classes[] = 'XLite\Logic\Export\Step\AttributeValues\AttributeValueSelect';
            $classes[] = 'XLite\Logic\Export\Step\AttributeValues\AttributeValueText';
        }

        foreach ($classes as $c) {
            $class = new $c;
            if ($found = (0 < $class->count())) {
                break;
            }
        }

        return !$found;
    }

    /**
     * Check - charset enabled or not
     *
     * @return boolean
     */
    protected function isCharsetEnabled()
    {
        return \XLite\Core\Iconv::getInstance()->isValid();
    }

    /**
     * Check download files available or not
     *
     * @return boolean
     */
    protected function downloadFilesAvailable()
    {
        if ($this->getGenerator()) {
            foreach ($this->getGenerator()->getDownloadableFiles() as $path) {
                if (preg_match('/\.csv$/Ss', $path)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get export state
     *
     * @return boolean
     */
    public function isExportLocked()
    {
        return \XLite\Logic\Export\Generator::isLocked();
    }
}
