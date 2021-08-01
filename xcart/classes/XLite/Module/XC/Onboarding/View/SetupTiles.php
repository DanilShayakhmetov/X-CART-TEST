<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Onboarding\View;

use XLite\Core\Auth;
use XLite\Core\Config;
use XLite\Model\Role\Permission;

/**
 * Setup tiles
 * @ListChild (list="dashboard-center", weight="50", zone="admin")
 */
class SetupTiles extends \XLite\View\AView
{
    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        return [
            'main'
        ];
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        return [
            $this->getDir() . '/controller.js',
        ];
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return [
            $this->getDir() . '/style.less',
        ];
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/Onboarding/setup_tiles';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return \XLite::getInstance()->getOptions(['service', 'is_cloud'])
            && Auth::getInstance()->isPermissionAllowed(Permission::ROOT_ACCESS);
    }
}