<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\Base;

/**
 * Rich one-choice selector
 */
abstract class Rich extends \XLite\View\FormField\Select\Regular
{
    /**
     * Widget params set
     */
    const PARAM_DISABLE_SEARCH  = 'disableSearch';
    const PARAM_MULTIPLE        = 'multiple';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/js/rich.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/css/rich.less';

        return $list;
    }
    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][] = 'js/chosen.jquery.js';

        $list[static::RESOURCE_CSS][] = 'css/chosen/chosen.css';

        return $list;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_DISABLE_SEARCH    => new \XLite\Model\WidgetParam\TypeBool(
                'Disable search flag', $this->getDefaultDisableSearch(), false
            ),
            static::PARAM_MULTIPLE          => new \XLite\Model\WidgetParam\TypeBool(
                'Multiple select', false
            ),
        );
    }

    /**
     * Search is enabled by default
     *
     * @return boolean
     */
    protected function getDefaultDisableSearch()
    {
        return false;
    }

    /**
     * Prepare attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function prepareAttributes(array $attrs)
    {
        $attrs = parent::prepareAttributes($attrs);

        $attrs['class'] = (empty($attrs['class']) ? '' : $attrs['class'] . ' ') . 'rich';

        return $attrs;
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        $result = parent::setCommonAttributes($attrs)
            + array(
                'data-placeholder'    => static::t('Select options'),
                'data-selected-text'  => static::t('# selected'),
                'data-disable-search' => $this->getParam(static::PARAM_DISABLE_SEARCH),
            );

        if ($this->getParam(static::PARAM_MULTIPLE)) {
            $result = $result + array('multiple' => 'multiple');
        }

        return $result;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'select.rich.twig';
    }

}

