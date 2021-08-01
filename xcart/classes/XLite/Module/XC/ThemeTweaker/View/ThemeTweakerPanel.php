<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

use XLite\Core\PreloadedLabels\ProviderInterface;
use XLite\Module\XC\ThemeTweaker\Core\ThemeTweaker;

/**
 * Main panel of admin editing mode
 *
 * @ListChild (list="layout.main", zone="customer", weight="0")
 */
class ThemeTweakerPanel extends \XLite\View\AView implements ProviderInterface
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ThemeTweaker/themetweaker_panel';
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

        $list[] = [
            'file'  => $this->getDir() . '/panel_style.less',
            'media' =>  'screen',
            'merge' =>  'bootstrap/css/bootstrap.less',
        ];
        $list[] = [
            'file'  => $this->getDir() . '/animations.less',
            'media' =>  'screen',
            'merge' =>  'bootstrap/css/bootstrap.less',
        ];

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

        $list[] = $this->getDir() . '/getters.js';
        $list[] = $this->getDir() . '/modals.js';
        $list[] = $this->getDir() . '/store.js';
        $list[] = $this->getDir() . '/panel.js';
        $list[] = $this->getDir() . '/store/actions.js';
        $list[] = $this->getDir() . '/store/webmaster.js';
        $list[] = $this->getDir() . '/store/layout_editor.js';
        $list[] = $this->getDir() . '/store/inline_editor.js';
        $list[] = $this->getDir() . '/store/custom_css.js';
        $list[] = $this->getDir() . '/panel/actions.js';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS] = array_merge($list[static::RESOURCE_JS], static::getVueLibraries());
        $list[static::RESOURCE_JS][] = 'js/keymaster.min.js';
        $list[static::RESOURCE_JS][] = 'js/keymaster.adapter.js';

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
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }

    /**
     * @return string
     */
    protected function getThemeTweakerMode()
    {
        $mode = ThemeTweaker::getInstance()->getCurrentMode();

        if ($mode === ThemeTweaker::MODE_INLINE_EDITOR && !ThemeTweaker::getInstance()->isTargetAllowedInInlineEditorMode()) {
            return null;
        }

        return $mode;
    }

    /**
     * Returns CSS class string for tab
     *
     * @param string $tab Themetweaker panel tab identifier
     * @return string
     */
    protected function getTabClass($tab)
    {
        if ($tab === $this->getThemeTweakerMode()) {
            return 'active';
        }

        return '';
    }

    /**
     * Returns CSS attributes array for tab
     *
     * @param string $tab Themetweaker panel tab identifier
     * @return string
     */
    protected function getTabAttributes($tab)
    {
        return ['@click' => 'switchTab("' . $tab . '")'];
    }

    /**
     * Returns CSS attributes array for tab
     *
     * @param string $tab Themetweaker panel tab identifier
     * @return string
     */
    protected function getTabDisabledTooltip($tab)
    {
        if ($tab === ThemeTweaker::MODE_INLINE_EDITOR) {
            return 'Go to different page to edit the content';
        }

        return '';
    }

    /**
     * Checks if certain tab is available right now
     *
     * @param string $tab Themetweaker panel tab identifier
     * @return boolean
     */
    protected function isTabAvailable($tab)
    {
        if ($tab === ThemeTweaker::MODE_INLINE_EDITOR) {
            return ThemeTweaker::getInstance()->isTargetAllowedInInlineEditorMode();
        }

        return true;
    }

    /**
     * Array of labels in following format.
     *
     * 'label' => 'translation'
     *
     * @return mixed
     */
    public function getPreloadedLanguageLabels()
    {
        $list = array(
            'Discard changes',
            'Pick templates from page',
            'Drag-n-drop blocks',
            'Use custom CSS',
            'Highlight labels',
            'Enable',
            'Disable',
            'Save changes',
            'Exit editor',
            'Exiting...',
            'Changes were successfully saved',
            'Unable to save changes',
            'You have unsaved changes. Are you really sure to exit?',
            'Layout block',
            'xlite-translation-popover.help',
            'Revert to default',
            'Delete custom file',
            'new template',
            'type filename and press Enter',
            'template weight'
        );

        $data = array();
        foreach ($list as $name) {
            $data[$name] = static::t($name);
        }

        return $data;
    }
}

