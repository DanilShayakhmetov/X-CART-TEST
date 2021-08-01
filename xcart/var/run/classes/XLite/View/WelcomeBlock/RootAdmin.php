<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\WelcomeBlock;

/**
 * Root Admin's 'Welcome...' inner block widget
 * 
 * @ListChild (list="dashboard-center", weight="50", zone="admin")
 */
class RootAdmin extends \XLite\View\AWelcomeBlock
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('main'));
    }

    protected function getInnerViewList()
    {
        return 'welcome-block.root';
    }

    protected function getBlockName()
    {
        return 'root';
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'welcome_block/root';
    }

    /**
     * Check block visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return false && $this->isRootAccess() && $this->isNotHiddenByUser();
    }
}