<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\Page;

use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module;

/**
 * Google Tag manager promo
 */
class GoogleTagManagerPromo extends \XLite\View\AView
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/XC/ThemeTweaker/google_tag_manager/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/google_tag_manager/body.twig';
    }

    /**
     * Get module name
     *
     * @return string
     */
    protected function getModuleName()
    {
        return 'XC\\GoogleTagManager';
    }

    /**
     * Check module installed
     *
     * @return boolean
     */
    protected function isModuleEnabled()
    {
        return Manager::getRegistry()->isModuleEnabled($this->getModuleName());
    }

    /**
     * @return boolean
     */
    protected function isModuleConfigured()
    {
        return $this->isModuleEnabled() && \XLite\Core\Config::getInstance()->XC->GoogleTagManager->container_id;
    }

    /**
     * @return string
     */
    protected function getModuleConfigURL()
    {
        list($author, $name) = explode('\\', $this->getModuleName());

        return Manager::getRegistry()->getModuleSettingsUrl($author, $name);
    }

    /**
     * @return string
     */
    protected function getModuleURL()
    {
        list($author, $name) = explode('\\', $this->getModuleName());

        return Manager::getRegistry()->getModuleServiceURL($author, $name);
    }

    /**
     * Returns current target
     *
     * @return string
     */
    protected function getCurrentTarget()
    {
        return \XLite\Core\Request::getInstance()->target;
    }
}
