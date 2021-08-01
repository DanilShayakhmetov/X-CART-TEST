<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Admin's 'Welcome...' block widget
 */
abstract class AWelcomeBlock extends \XLite\View\Dialog
{
    /**
     * Returns the name of the inner view list with content. The format convention is to use 'welcome-block.something'.
     * 
     * @return string
     */
    abstract protected function getInnerViewList();

    /**
     * Returns block registered name, used in persistance mechanism and css-naming
     *
     * @return string
     */
    abstract protected function getBlockName();

    /**
     * Get box class
     *
     * @return string
     */
    protected function getBoxClass()
    {
        return 'admin-welcome ' . $this->getBlockName();
    }

    /**
     * Get close target
     *
     * @return string
     */
    protected function getCloseTarget()
    {
        return 'main';
    }

    /**
     * Get close action
     *
     * @return string
     */
    protected function getCloseAction()
    {
        return 'hide_welcome_block';
    }

    /**
     * Add widget specific CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'welcome_block/style.css';

        return $list;
    }

    /**
     * Add widget specific JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'welcome_block/controller.js';

        return $list;
    }

    /**
     * Check if the current admin user has the root access
     *
     * @return boolean
     */
    protected function isRootAccess()
    {
        return \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }

    /**
     * Check 'Admin welcome' block visibility
     *
     * @return boolean
     */
    protected function isNotHiddenByUser()
    {
        $profileId = \XLite\Core\Auth::getInstance()->getProfile()->getProfileId();

        return !isset(\XLite\Core\Session::getInstance()->closedBlocks[$this->getBlockName()])
            && !isset(\XLite\Core\TmpVars::getInstance()->closedBlocks[$profileId][$this->getBlockName()]);
    }

    protected function getDefaultTemplate()
    {
        return 'welcome_block/body.twig';
    }
}

