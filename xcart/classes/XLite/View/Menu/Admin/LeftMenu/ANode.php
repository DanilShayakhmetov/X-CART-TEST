<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LeftMenu;

/**
 * Node
 */
abstract class ANode extends \XLite\View\Menu\Admin\ANode
{
    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'left_menu';
    }

    /**
     * Return list name
     *
     * @return string
     */
    protected function getListName()
    {
        return 'menu.' . $this->getParam(static::PARAM_LIST);
    }

    /**
     * @return \XLite\View\AView
     */
    protected function getActionWidget()
    {
        return null;
    }

    /**
     * Return CSS class for the link item
     *
     * @return string
     */
    protected function getCSSClass()
    {
        return parent::getCSSClass() . ' text-capitalize';
    }
}
