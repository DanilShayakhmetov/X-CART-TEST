<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\ThemeTweaker;

use XLite\Module\XC\ThemeTweaker\Core\TemplateObjectProvider;

/**
 * Code widget
 */
class TemplateCode extends \XLite\View\AView
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
        return $this->getDir() . '/template_code.twig';
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/template_code.js';

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
        $list[] = $this->getDir() . '/template_code.css';

        return $list;
    }

    /**
     * Retrieve content from the template file
     *
     * @return mixed
     */
    protected function getTemplateContent()
    {
        $value = '';

        if ($this->getTemplateObject() && $this->getTemplateObject()->getId()) {
            $localPath = $this->getTemplateObject()->getTemplate();
        } else {
            $localPath = $this->getTemplatePath();
        }

        if ($localPath) {
            $value = \Includes\Utils\FileManager::read(\LC_DIR_SKINS . $localPath);
        }

        return $value;
    }

    /**
     * @return integer
     */
    protected function getTemplateObjectId()
    {
        return $this->getTemplateObject()
            ? $this->getTemplateObject()->getId()
            : null;
    }

    protected function getWidgetData()
    {
        return json_encode([
            'templateId' => $this->getTemplateObjectId()
        ]);
    }

    /**
     * @return \XLite\Module\XC\ThemeTweaker\Model\Template
     */
    protected function getTemplateObject()
    {
        return TemplateObjectProvider::getInstance()->getTemplateObject();
    }

    /**
     * @return string
     */
    private function getTemplatePath()
    {
        return TemplateObjectProvider::getInstance()->getTemplatePath();
    }
}
