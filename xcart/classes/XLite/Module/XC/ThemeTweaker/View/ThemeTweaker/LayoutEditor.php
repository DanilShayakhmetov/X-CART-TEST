<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\ThemeTweaker;

use Includes\Utils\Module\Manager;
use XLite\Core\Layout;

/**
 * Main panel of layout editing mode
 *
 * @ListChild (list="themetweaker-panel--content", weight="100")
 */
class LayoutEditor extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ThemeTweaker/themetweaker/layout_editor';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/panel.twig';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/panel_style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/panel_parts/layout_options.js';
        $list[] = $this->getDir() . '/layout_editor_panel.js';

        return $list;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->isInLayoutMode();
    }

    /**
     * @return bool
     */
    protected function isNotOptimalTarget()
    {
        return !in_array($this->getTarget(), ['main', 'category'], true);
    }

    /**
     * Get finishOperateAs action url
     * 
     * @return string
     */
    protected function getFinishOperateAsUrl()
    {
        return $this->buildURL('login', 'logoff');
    }

    /**
     * Returns current used layout preset key
     * @return string
     */
    protected function getCurrentLayoutPreset()
    {
        return Layout::getInstance()->getCurrentLayoutPreset();
    }

    /**
     * Check if logo configuration is available
     *
     * @return bool
     */
    protected function isLogoConfigurable()
    {
        return Manager::getRegistry()->isModuleEnabled('CDev\SimpleCMS');
    }

    /**
     * @return boolean
     */
    protected function isResetAvailable()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\ViewList')->hasOverriddenRecords($this->getCurrentLayoutPreset());
    }

    /**
     * @param string $key Field name
     *
     * @return \XLite\Model\Image\Common\Logo
     */
    protected function getImageObject($key)
    {
        /** @var \XLite\Model\Repo\Image\Common\Logo $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Image\Common\Logo');

        switch($key) {
            case 'logo':
                return $repo->getLogo();
            case 'favicon':
                return $repo->getFavicon();
            case 'appleIcon':
                return $repo->getAppleIcon();
            default:
                return null;
        }
    }

    /**
     * @return string
     */
    protected function getImageMaxWidth()
    {
        return '70';
    }

    /**
     * @return string
     */
    protected function getImageMaxHeight()
    {
        return '70';
    }
}

