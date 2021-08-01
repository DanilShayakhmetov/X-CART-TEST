<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\ThemeTweaker;

use Includes\Utils\Module\Manager;
use XLite\Module\XC\ThemeTweaker\Core;

use XLite\Core\PreloadedLabels\ProviderInterface;

/**
 * Widget with resources for inline content editing
 *
 * @ListChild (list="themetweaker-panel--content", weight="100")
 */
class InlineEditor extends \XLite\View\AView implements ProviderInterface
{
    /**
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = array();

        $list[static::RESOURCE_JS][]    = 'froala-editor/js/froala_editor.pkgd.min.js';
        $list[static::RESOURCE_JS][]    = 'froala-editor/js/froala_editor.activate.js';
        $list[static::RESOURCE_CSS][]   = 'froala-editor/css/froala_editor.pkgd.min.css';
        $list[static::RESOURCE_JS][]    = $this->getEditorLanguageResource();

        return $list;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = array();

        $list[] = $this->getDir() . '/panel_style.css';
        $list[] = $this->getDir() . '/editor_style.css';

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = array();

        $list[] = $this->getDir() . '/inline_editable_controller.js';
        $list[] = $this->getDir() . '/panel_controller.js';

        return $list;
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return 'modules/XC/ThemeTweaker/themetweaker/inline_editable';
    }

    /**
     * Return resource structure for content editor language file.
     * By default there are several ready-to-use language files from content editor project.
     * The translation module is able to use its own language validation file.
     * It should decorate this method for this case.
     *
     * @return array
     */
    protected function getEditorLanguageResource()
    {
        return array(
            'file' => $this->getEditorLanguageFile(),
            'no_minify' => true,
        );
    }

    /**
     * Return content editor language file path.
     *
     * @return string
     */
    protected function getEditorLanguageFile()
    {
        return 'froala-editor/js/languages/'
            . $this->getCurrentLanguageCode()
            . '.js';
    }

    /**
     * Gets current language code and fixes it in case of en-GB and similar.
     *
     * @return string
     */
    protected function getCurrentLanguageCode()
    {
        $code = $this->getCurrentLanguage()->getCode();

        switch ($code) {
            case 'en':
                return 'en_gb';

            case 'pt':
                return 'pt_pt';

            case 'zh':
                return 'zh_cn';
            
            default:
                return $code;
        }
    }

    /**
     * Checks if the incompatible tinymce module is enabled
     *
     * @return boolean
     */
    public static function isTinymceWarningVisible()
    {
        return Manager::getRegistry()->isModuleEnabled('CDev', 'TinyMCE');
    }

    /**
     * Checks if widget should be rendered
     * 
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && Core\ThemeTweaker::getInstance()->isInInlineEditorMode();
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
     * Get preloaded labels
     *
     * @return array
     */
    public function getPreloadedLanguageLabels()
    {
        $list = array(
            'Enable',
            'Disable',
            'Save changes',
            'Exit product preview',
            'Exiting...',
            'Changes were successfully saved',
            'Unable to save changes',
            'You are now in preview mode',
            'You have unsaved changes. Are you really sure to exit the preview?',
            'Inline editor is unavailable due to TinyMCE',
            'Changes may be incompatible with TinyMCE. Are you sure to proceed?'
        );

        $data = array();
        foreach ($list as $name) {
            $data[$name] = static::t($name);
        }

        return $data;
    }
}
