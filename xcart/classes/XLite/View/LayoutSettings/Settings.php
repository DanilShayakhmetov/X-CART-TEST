<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LayoutSettings;

use Includes\Utils\Module\Manager;
use XLite\Core\Skin;

/**
 * Layout settings
 */
class Settings extends \XLite\View\AView
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'layout_settings/settings/style.less';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'layout_settings/controller.js';

        return $list;
    }

    /**
     * Returns current skin
     *
     * @return \Includes\Utils\Module\Module
     */
    public function getCurrentSkin()
    {
        return Skin::getInstance()->getCurrentSkinModule();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'layout_settings/settings/body.twig';
    }

    /**
     * Returns preview image url
     *
     * @return string
     */
    protected function getPreviewImageURL()
    {
        return Skin::getInstance()->getCurrentLayoutPreview(\XLite\Core\Layout::LAYOUT_GROUP_HOME);
    }

    /**
     * Returns current skin name
     *
     * @return string
     */
    protected function getCurrentSkinName()
    {
        return Skin::getInstance()->getSkinDisplayName();
    }

    /**
     * Check show settings
     *
     * @return boolean
     */
    protected function showSettingsForm()
    {
        /** @var \Includes\Utils\Module\Module $module */
        $module = $this->getCurrentSkin();

        return $module && $module->showSettingsForm;
    }

    /**
     * Check has custom options
     *
     * @return boolean
     */
    protected function getSettingsForm()
    {
        /** @var \Includes\Utils\Module\Module $module */
        $module = $this->getCurrentSkin();

        return Manager::getRegistry()->getModuleSettingsUrl($module->id);
    }
}
