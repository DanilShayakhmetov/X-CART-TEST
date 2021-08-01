<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\ThemeTweaker;

use XLite\Module\XC\ThemeTweaker\Core\ThemeTweaker;

/**
 * Code widget
 *
 * @ListChild (list="themetweaker-panel--content", weight="100")
 */
class WebmasterMode extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ThemeTweaker/themetweaker/webmaster_mode';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/webmaster_mode.twig';
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/new_template_button.js';
        $list[] = $this->getDir() . '/webmaster_mode.js';

        return $list;
    }
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/webmaster_mode.css';

        return $list;
    }

    public function isVisible()
    {
        return \XLite\Module\XC\ThemeTweaker\Core\ThemeTweaker::getInstance()->isInWebmasterMode();
    }

    /**
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        if (ThemeTweaker::getInstance()->isInWebmasterMode()) {
            $list[static::RESOURCE_JS][] = 'jstree/jstree.js';
            $list[static::RESOURCE_JS][] = 'modules/XC/ThemeTweaker/template_editor/tree-view.js';
            $list[static::RESOURCE_JS][] = 'modules/XC/ThemeTweaker/template_editor/template-navigator-customer.js';
            $list[static::RESOURCE_CSS][] = 'jstree/themes/default/style.min.css';
            $list[static::RESOURCE_CSS][] = 'modules/XC/ThemeTweaker/template_editor/template-navigator.css';
        }

        return $list;
    }

    protected function getInterface()
    {
        return \XLite::CUSTOMER_INTERFACE;
    }

    /**
     * @return array
     */
    protected function getJstreeCacheParams()
    {
        return [
            \XLite\Core\Session::getInstance()->getID(),
            \XLite\Core\Session::getInstance()->themetweaker_cache_key,
            \XLite::getController()->getTarget(),
        ];
    }

    /**
     * Returns Jstree cache key (cache is used for scroll position, last opened template etc.)
     *
     * @return string
     */
    protected function getJstreeCacheKey()
    {
        return 'jstree' . md5(serialize($this->getJstreeCacheParams()));
    }
}
