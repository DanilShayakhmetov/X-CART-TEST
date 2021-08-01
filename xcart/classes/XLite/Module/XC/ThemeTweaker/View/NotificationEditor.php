<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

use XLite\Core\Mail\Sender;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\Data;
use XLite\Module\XC\ThemeTweaker\Core\Notifications\DataPreProcessor;

/**
 * Theme tweaker template page view
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class NotificationEditor extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'notification_editor';

        return $list;
    }

    protected function isVisible()
    {
        return parent::isVisible() && $this->getDataSource();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/notification_editor/body.twig';
    }

    /**
     * @return string
     */
    protected function getNotificationContent()
    {
        $content = Sender::getNotificationEditableContent(
            $this->getDir(),
            $this->prepareData($this->getData()),
            $this->getInterface()
        );

        return \XLite::getController()->isTemplateFailed()
            ? $this->prepareFailedTemplates(\XLite::getController()->getFailedTemplates())
            : $content;
    }

    /**
     * @param array $templates
     *
     * @return string
     */
    protected function prepareFailedTemplates(array $templates)
    {
        return sprintf(
            '<div class="notification_editor">%s:<br> %s</div>',
            static::t('Templates error'),
            implode('<br>', array_map(function ($template) {
                if (mb_strpos($template, LC_DIR_SKINS) === 0) {
                    $template = mb_substr($template, mb_strlen(LC_DIR_SKINS));
                }

                return $template;
            }, $templates))
        );
    }

    /**
     * Return true if current template's content is empty
     *
     * @return boolean
     */
    protected function isEmptyTemplateContent()
    {
        $result = false;

        $path = $this->getNotificationTemplatePath();

        if ($path) {
            $content = \Includes\Utils\FileManager::read($path);

            if (preg_match('/^[\s\n]*({#[\s\S]*[^#]#})?([\s\S]*?)$/', $content, $m)) {
                $result = empty($m[2]);
            }
        }

        return $result;
    }

    /**
     * Get notification template full or local path
     *
     * @return string
     */
    protected function getNotificationTemplatePath($local = false)
    {
        $fullPath = $this->getNotificationRootTemplate($this->getDataSource()->getDirectory(), $this->getInterface());

        return $local
            ? substr($fullPath, strlen(\LC_DIR_SKINS))
            : $fullPath;
    }

    /**
     * Get URL for 'Add TWIG code' button
     *
     * @return boolean
     */
    protected function getAddTwigCodeButtonURL()
    {
        return $this->buildURL(
            'theme_tweaker_template',
            '',
            [
                'template'       => $this->getNotificationTemplatePath(true),
                'interface'      => 'mail',
                'innerInterface' => $this->getInterface(),
            ]
        );
    }

    /**
     * Get URL for 'Preview full email' button
     *
     * @return boolean
     */
    protected function getPreviewURL()
    {
        return $this->buildURL(
            'notification',
            '',
            [
                'templatesDirectory' => $this->getDataSource()->getDirectory(),
                'page'               => $this->getInterface(),
                'preview'            => true,
            ]
        );
    }

    /**
     * @param $data
     *
     * @return array
     */
    protected function prepareData(array $data)
    {
        return DataPreProcessor::prepareDataForNotification($this->getDir(), $data);
    }

    /**
     * @return Data
     */
    protected function getDataSource()
    {
        return \XLite::getController()->getDataSource();
    }

    /**
     * @return mixed
     */
    protected function getDir()
    {
        return \XLite\Core\Request::getInstance()->templatesDirectory;
    }

    /**
     * @return array
     */
    protected function getData()
    {
        return $this->getDataSource()->getData();
    }

    /**
     * @return string
     */
    protected function getInterface()
    {
        return \XLite\Core\Request::getInstance()->interface;
    }
}
