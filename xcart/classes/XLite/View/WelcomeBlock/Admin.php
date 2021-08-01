<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\WelcomeBlock;

/**
 * Admin's 'Welcome...' block widget
 *
 * @ListChild (list="dashboard-center", weight="50", zone="admin")
 */
class Admin extends \XLite\View\AWelcomeBlock
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
        return 'welcome-block.non-root';
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'welcome_block/non-root';
    }

    /**
     * Check block visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return !$this->isRootAccess();
    }

    /**
     * Return the roles of the current admin user
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function getRoles()
    {
        return \XLite\Core\Auth::getInstance()->getProfile()->getRoles();
    }

    protected function getBlockName()
    {
        return 'non-root';
    }
}