<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FroalaEditor\View\FormField\Textarea;

use XLite\Core\Layout;

/**
 * Froala textarea widget
 *
 * https://github.com/xcart/wysiwyg-editor
 */
class MailAdvanced extends \XLite\View\FormField\Textarea\Advanced
{
    /**
     * Return structure of configuration for JS TinyMCE library
     *
     * @return array
     */
    protected function getFroalaConfiguration()
    {
        return array_merge(parent::getFroalaConfiguration(), [
            'linkAutoPrefix' => ''
        ]);
    }

    /**
     * Return list of froala toolbar buttons
     *
     * @return array
     */
    protected function getFroalaToolbarButtons()
    {
        return [
            'fontSize',
            '|', 'bold', 'italic', 'underline', 'strikeThrough', 'color',
            '|', 'paragraphFormat',
            '|', 'align',
            '|', 'undo', 'redo', 'html',
        ];
    }

    /**
     * Provides style files for iframe mode. Destined to replicate customer zone style.
     *
     * @return array
     */
    protected function getIframeStyleFiles()
    {
        return array_merge([
            Layout::getInstance()->getResourceWebPath('reset.css', Layout::WEB_PATH_OUTPUT_SHORT, \XLite::MAIL_INTERFACE)
        ], parent::getIframeStyleFiles());
    }

    /**
     * Returns compiled customer zone style file url.
     *
     * @return string
     */
    protected function getCustomerLessStyles()
    {
        $lessParser = \XLite\Core\LessParser::getInstance();

        $customerLESS = [
            [
                'file'      => Layout::getInstance()->getResourceFullPath('common/style.less', \XLite::MAIL_INTERFACE),
                'media'     => 'screen',
                'weight'    => 0,
                'filelist'  => [
                    'common/style.less',
                ],
                'interface' => \XLite::MAIL_INTERFACE,
                'original'  => 'common/style.less',
                'url'       => Layout::getInstance()->getResourceWebPath('common/style.less', Layout::WEB_PATH_OUTPUT_SHORT, \XLite::MAIL_INTERFACE),
                'less'      => true,
            ],
            [
                'file'      => Layout::getInstance()->getResourceFullPath('mail/core.less', \XLite::COMMON_INTERFACE),
                'media'     => 'screen',
                'weight'    => 0,
                'filelist'  => [
                    'mail/core.less',
                ],
                'interface' => \XLite::COMMON_INTERFACE,
                'original'  => 'mail/core.less',
                'url'       => Layout::getInstance()->getResourceWebPath('mail/core.less', Layout::WEB_PATH_OUTPUT_SHORT, \XLite::COMMON_INTERFACE),
                'less'      => true,
            ],
            [
                'file'      => Layout::getInstance()->getResourceFullPath('modules/XC/FroalaEditor/mail_textarea.less', \XLite::COMMON_INTERFACE),
                'media'     => 'screen',
                'weight'    => 0,
                'filelist'  => [
                    'modules/XC/FroalaEditor/mail_textarea.less',
                ],
                'interface' => \XLite::COMMON_INTERFACE,
                'original'  => 'modules/XC/FroalaEditor/mail_textarea.less',
                'url'       => Layout::getInstance()->getResourceWebPath('modules/XC/FroalaEditor/mail_textarea.less', Layout::WEB_PATH_OUTPUT_SHORT, \XLite::COMMON_INTERFACE),
                'less'      => true,
            ],
        ];

        // Customer LESS files parsing
        $lessParser->setInterface('default');

        $lessParser->setHttp('http');
        $style = $lessParser->makeCSS($customerLESS);

        if ($style && isset($style['url'])) {
            return $style['url'];
        }

        return null;
    }

    /**
     * Returns specific froala editor styles to be used inside iframe
     *
     * @return array
     */
    protected function getFroalaEditorStyles()
    {
        return [];
    }
}
