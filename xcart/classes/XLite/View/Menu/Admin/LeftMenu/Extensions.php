<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LeftMenu;

/**
 * Extensions node
 */
class Extensions extends \XLite\View\Menu\Admin\LeftMenu\Node
{
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = $this->getDir() . '/controller.js';

        return $list;
    }

    /**
     * @return \XLite\View\AView
     */
    protected function getActionWidget()
    {
        return $this->getWidget([], 'XLite\View\Button\Menu\Extensions');
    }

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/extensions';
    }
}
