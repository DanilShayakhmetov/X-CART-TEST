<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin;

use XLite\Core\View\DynamicWidgetInterface;

/**
 * ExpandedMenuNodeClass dynamic widget renders 'active pre-expanded' css class on a menu node if it is active
 */
class ExpandedMenuNodeClass extends \XLite\View\AView implements DynamicWidgetInterface
{
    const PARAM_DECIDER = 'decider';
    const PARAM_NAME = 'name';

    /**
     * Display widget with the default or overriden template.
     *
     * @param $template
     */
    protected function doDisplay($template = null)
    {
        $target = \XLite\Core\Request::getInstance()->target;
        $name = $this->getParam(static::PARAM_NAME);

        if ($this->getSelectedDecider()->isSelected($target, $name)) {
            echo 'active';
        }

        if ($this->getSelectedDecider()->isExpanded($target, $name)) {
            echo 'active pre-expanded';
        }
    }

    /**
     * @return SelectedDecider
     */
    protected function getSelectedDecider()
    {
        return $this->getParam(static::PARAM_DECIDER);
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
            static::PARAM_DECIDER => new \XLite\Model\WidgetParam\TypeObject(
                'SelectedDecider', null, false, '\XLite\View\Menu\Admin\SelectedDecider'
            ),
            static::PARAM_NAME => new \XLite\Model\WidgetParam\TypeString('Name', null),
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return null;
    }
}
