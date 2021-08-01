<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LayoutSettings;

use \XLite\Core\Layout;
use XLite\Core\Skin;

/**
 * Layout settings
 */
abstract class LayoutTypeSelectorAbstract extends \XLite\View\AView
{
    /**
     * Returns styles
     * 
     * @return array
     */
    public function getCSSFiles()
    {
        return array(
            'layout_settings/parts/layout_settings.type_selector.css'
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout_settings/parts/layout_settings.type_selector.twig';
    }

    protected function getLayoutGroups()
    {
        return Layout::getInstance()->getAvailableLayoutTypes();
    }

    protected function getLayoutTypeLabel($group)
    {
        return Layout::getInstance()->getLayoutTypeLabelByGroup($group);
    }

    protected function getLayoutType($group)
    {
        return Layout::getInstance()->getLayoutType($group);
    }

    protected function getLayoutTypes($group)
    {
        $types = Layout::getInstance()->getAvailableLayoutTypes();

        return isset($types[$group]) ? $types[$group] : array();
    }
}
