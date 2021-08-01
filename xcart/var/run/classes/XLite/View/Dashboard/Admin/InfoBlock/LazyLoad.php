<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Dashboard\Admin\InfoBlock;

/**
 * @ListChild (list="dashboard-sidebar", weight="200", zone="admin")
 */
class LazyLoad extends \XLite\View\Base\ALazyLoad
{
    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'dashboard/info_block/controller.js';

        return $list;
    }

    /**
     * Returns default lazy class
     *
     * @return string
     */
    protected function getDefaultLazyClass()
    {
        return InfoBlock::class;
    }

    /**
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && \XLite\Core\Auth::getInstance()->isAdmin();
    }
}
