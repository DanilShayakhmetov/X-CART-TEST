<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;

use Includes\Utils\Module\Manager;
use XLite\Model\TemporaryFile;

/**
 * ThemeTweaker controller
 */
class LayoutEdit extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        $list = parent::defineFreeFormIdActions();
        $list[] = 'disable';

        return $list;
    }

    /**
     * Applies layout editor changes
     */
    protected function doActionApplyChanges()
    {
        $preset = \XLite\Core\Request::getInstance()->preset;
        $changes = \XLite\Core\Request::getInstance()->changes;

        $shouldReload = false;

        if ($preset && $changes) {
            \XLite\Core\Database::getRepo('XLite\Model\ViewList')->updateOverrides($preset, $changes);
        }

        if ($this->isLogoConfigurable()) {
            $logoFields = ['logo', 'favicon', 'appleIcon'];

            foreach ($logoFields as $field) {
                if (\XLite\Core\Request::getInstance()->{$field}) {
                    $data = \XLite\Core\Request::getInstance()->{$field};

                    $isDelete = isset($data['is_delete']) ? $data['is_delete'] === 'true' : false;
                    $hasTempId = $data['temp_id'] ?? false;

                    switch (true) {
                        case $isDelete && !$hasTempId:
                            if ($this->deleteLogoImage($field)) {
                                $this->updateLogoConfigValue($field, '');
                                $shouldReload = true;
                            }
                            break;
                        case !$isDelete && $hasTempId:
                            $result = $this->uploadLogoImage($field);
                            if ($result) {
                                $this->updateLogoConfigValue($field, $result);
                                $shouldReload = true;
                            }
                            break;
                        case $isDelete && $hasTempId:
                            $shouldReload = true;
                            break;
                    }

                    if ('logo' === $field && isset($data['alt'])) {
                        $this->updateLogoConfigValue('logo_alt', $data['alt']);
                        $shouldReload = true;
                    }
                }
            }
        }

        if ($shouldReload) {
            $this->setReturnURL($this->getReturnURL());
            $this->setHardRedirect(true);
        } else {
            $this->set('silent', true);
            $this->setSuppressOutput(true);
        }
    }

    /**
     * Applies layout editor changeset to the view lists repo
     */
    protected function doActionResetLayout()
    {
        $preset = \XLite\Core\Request::getInstance()->preset;

        if ($preset) {
            \XLite\Core\Database::getRepo('XLite\Model\ViewList')->resetOverrides($preset);
        }

        $this->setReturnURL($this->getReturnURL());
        $this->setHardRedirect(true);
    }

    /**
     * Change layout
     *
     * @return void
     */
    protected function doActionSwitchLayoutType()
    {
        $group = \XLite\Core\Request::getInstance()->group;
        $type = \XLite\Core\Request::getInstance()->type;
        \XLite\Core\Layout::getInstance()->switchLayoutType($group, $type);

        $this->setReturnURL($this->getReturnURL());
        $this->setHardRedirect(true);
    }

    /**
     * Disable editor
     *
     * @return void
     */
    protected function doActionDisable()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            array(
                'category' => 'XC\ThemeTweaker',
                'name'     => 'layout_mode',
                'value'    => false,
            )
        );

        \XLite\Core\TopMessage::addInfo('Layout editor is disabled');

        $this->setReturnURL($this->getReturnURL());

        $this->setHardRedirect(true);
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
     * @param string $key
     * @param mixed $value
     */
    protected function updateLogoConfigValue($key, $value)
    {
        \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption(
            array(
                'category' => 'CDev\SimpleCMS',
                'name'     => $key,
                'value'    => $value,
            )
        );
    }

    /**
     * @param $type
     * @return bool|string
     */
    protected function uploadLogoImage($type)
    {
        if (!$this->isLogoConfigurable()) {
            return false;
        }

        $data = \XLite\Core\Request::getInstance()->{$type};
        $optionValue = \XLite\Core\Config::getInstance()->CDev->SimpleCMS->{$type};

        /** @var \XLite\Model\TemporaryFile $temporaryFile */
        $temporaryFile = isset($data['temp_id'])
            ? \XLite\Core\Database::getRepo('\XLite\Model\TemporaryFile')->find($data['temp_id'])
            : null;

        if ($temporaryFile) {
            $imageType = $type;

            $subDir = \Includes\Utils\FileManager::getRelativePath(LC_DIR_IMAGES, LC_DIR) . LC_DS . 'simplecms' . LC_DS;
            $dir = LC_DIR . LC_DS . $subDir;
            $path = null;

            $realName = preg_replace('/([^a-zA-Z0-9_\-\.]+)/', '_', $temporaryFile->getFileName());
            $realName = $imageType . '_' . $realName;

            $validImage = $imageType === 'appleIcon'
                ? $this->isValidAppleIcon($temporaryFile)
                : $this->isValidImage($temporaryFile);

            if ($validImage) {
                if (\Includes\Utils\FileManager::isDirWriteable($dir) || \Includes\Utils\FileManager::mkdir($dir)) {

                    // Move uploaded file to destination directory
                    $path = \Includes\Utils\FileManager::move(
                        $temporaryFile->getStoragePath(),
                        $dir . LC_DS . $realName,
                        true
                    );

                    if ($path) {
                        if ($optionValue && basename($optionValue) !== $realName) {
                            // Remove old image file
                            \Includes\Utils\FileManager::deleteFile($dir . basename($optionValue));
                        }

                        $optionValue = $subDir . $realName;
                    }
                }

                if (!isset($path)) {
                    \XLite\Core\TopMessage::addError(
                        'The "{{file}}" file was not uploaded',
                        array('file' => $realName)
                    );
                }

            } else {
                if ($imageType === 'appleIcon') {
                    \XLite\Core\TopMessage::addError(
                        'The AppleIcon image could not be uploaded (Unallowed image type. Must be a .png image with the resolution of 192x192 px)',
                        array(
                            'file' => $temporaryFile->getFileName(),
                        )
                    );
                } else {
                    \XLite\Core\TopMessage::addError(
                        'The "{{file}}" file is not allowed image and was not uploaded. Allowed images are: {{extensions}}',
                        array(
                            'file' => $temporaryFile->getFileName(),
                            'extensions' => implode(', ', \Includes\Utils\FileManager::getImageExtensions()),
                        )
                    );
                }

                return false;
            }
        }

        return $optionValue;
    }

    /**
     * @param $type
     * @return bool|string
     */
    protected function deleteLogoImage($type)
    {
        if (!$this->isLogoConfigurable()) {
            return false;
        }

        $optionValue = \XLite\Core\Config::getInstance()->CDev->SimpleCMS->{$type};

        $subDir = \Includes\Utils\FileManager::getRelativePath(LC_DIR_IMAGES, LC_DIR) . LC_DS . 'simplecms' . LC_DS;
        $dir = LC_DIR . LC_DS . $subDir;

        if ($optionValue) {
            // Remove old image file
            \Includes\Utils\FileManager::deleteFile($dir . basename($optionValue));
        }

        return true;
    }

    /**
     * Check if file is valid image
     *
     * @param TemporaryFile $file
     * @return boolean
     */
    protected function isValidAppleIcon($file)
    {
        return $this->isValidImage($file)
            && $file->getExtension() === 'png'
            && $file->getWidth() === 192
            && $file->getHeight() === 192;
    }

    /**
     * @param $file
     * @return bool
     */
    protected function isValidImage(TemporaryFile $file)
    {
        return $file->isImage()
            && \Includes\Utils\FileManager::isImageExtension($file->getFileName())
            && \Includes\Utils\FileManager::isImage($file->getStoragePath());
    }

    /**
     * @param $path
     * @param $resolution
     *
     * @return bool
     */
    protected function isValidResolution($path, $resolution)
    {
        $data = @getimagesize($path);

        return is_array($data)
            ? $data[0] . 'x' . $data[1] === $resolution
            : true;
    }
}
