<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Core;

use XLite\Module\XC\ThemeTweaker\Controller\Admin\NotificationEditor;

/**
 * Layout manager
 */
 class Layout extends \XLite\Module\XC\VendorMessages\Core\Layout implements \XLite\Base\IDecorator
{
    const THEME_TWEAKER_CUSTOMER_INTERFACE = 'theme_tweaker/customer';
    const THEME_TWEAKER_MAIL_INTERFACE = 'theme_tweaker/mail';

    const THEME_TWEAKER_TEMPLATES_CACHE_KEY = 'theme_tweaker_templates';

    const THEME_TWEAKER_INTERFACES = [
        \XLite::CUSTOMER_INTERFACE,
        \XLite::CONSOLE_INTERFACE,
        \XLite::MAIL_INTERFACE,
        \XLite::COMMON_INTERFACE,
        \XLite::PDF_INTERFACE,
    ];

    private $disabledTemplates;

    protected function getDisabledTemplates()
    {
        if (null === $this->disabledTemplates) {
            $cacheDriver = \XLite\Core\Cache::getInstance()->getDriver();
            if (!$list = $cacheDriver->fetch(static::THEME_TWEAKER_TEMPLATES_CACHE_KEY)) {
                $templates = \XLite\Core\Database::getRepo('XLite\Module\XC\ThemeTweaker\Model\Template')->findBy([
                    'enabled' => false
                ]);

                $list = array_map(function ($template) {
                    /** @var \XLite\Module\XC\ThemeTweaker\Model\Template $template */
                    return ltrim(str_replace([
                        static::THEME_TWEAKER_CUSTOMER_INTERFACE,
                        static::THEME_TWEAKER_MAIL_INTERFACE
                    ], '', $template->getTemplate()), '/');
                }, $templates);

                $cacheDriver->save(
                    static::THEME_TWEAKER_TEMPLATES_CACHE_KEY,
                    $list,
                    \XLite\Core\Task\Base\Periodic::INT_1_WEEK
                );
            }

            $this->disabledTemplates = $list;
        }

        return $this->disabledTemplates;
    }

    public function isDisabledTemplate($template)
    {
        return in_array($template, $this->getDisabledTemplates());
    }

    /**
     * Get skin paths (file system and web)
     *
     * @param string  $interface        Interface code OPTIONAL
     * @param boolean $reset            Local cache reset flag OPTIONAL
     * @param boolean $baseSkins        Use base skins only flag OPTIONAL
     * @param boolean $allInnerInterfaces
     *
     * @return array
     */
    public function getSkinPaths($interface = null, $reset = false, $baseSkins = false, $allInnerInterfaces = false)
    {
        return 'custom' === $interface
            ? [
                [
                    'name' => 'custom',
                    'fs'   => rtrim(LC_DIR_VAR, LC_DS),
                    'web'  => 'var',
                ],
            ]
            : parent::getSkinPaths($interface, $reset, $baseSkins, $allInnerInterfaces);
    }

    public function getSkinRelativePathByLocalPath($localPath, $interface)
    {
        $full = $this->getFullPathByLocalPath($localPath, $interface);
        return substr($full, strlen(\LC_DIR_SKINS));
    }

    public function getFullPathByLocalPath($localPath, $interface)
    {
        $pathSkin = '';
        $shortPath = '';

        $interfaceByPath = $this->getInterfaceByLocalPath($localPath);

        foreach ($this->getSkinPaths($interfaceByPath) as $path) {
            if (strpos($localPath, $path['name']) === 0) {
                $pathSkin = $path['name'];
                $shortPath = substr($localPath, strpos($localPath, LC_DS, strlen($pathSkin)) + strlen(LC_DS));

                break;
            }
        }

        $skin = $this->getTweakerSkinByInterface($interface);

        return ($shortPath && $pathSkin)
            ? $this->getFullPathByShortPath($shortPath, $interface ?? $interfaceByPath, $skin ?: $pathSkin, $this->locale)
            : '';
    }

    protected function getFullPathByShortPath($shortPath, $interface, $skin, $locale = null)
    {
        $result = '';

        foreach ($this->getSkinPaths($interface ?: \XLite::CUSTOMER_INTERFACE) as $path) {
            if (strpos($path['name'], $skin) === 0
                && (null === $locale || $path['locale'] === $locale)
            ) {
                $result = $path['fs'] . LC_DS . $shortPath;

                break;
            }
        }

        return $result;
    }

    public function getInterfaceByLocalPath($localPath)
    {
        $result = null;

        foreach (static::THEME_TWEAKER_INTERFACES as $interface) {
            $paths = $this->getSkinPaths($interface, false, false, true);
            foreach ($paths as $path) {
                if (strpos($localPath, $path['name']) === 0) {

                    $result = $interface;
                    break;
                }
            }

            if ($result) {
                break;
            }
        }

        return $result;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getInnerInterfaceByLocalPath(string $path)
    {
        preg_match('#' . self::THEME_TWEAKER_MAIL_INTERFACE . '/(.*)/#U', $path, $matches);
        return $matches[1] ?? \XLite::CUSTOMER_INTERFACE;
    }

    public function getTweakerSkinByInterface($interface)
    {
        switch ($interface) {
            case \XLite::CUSTOMER_INTERFACE:
                return self::THEME_TWEAKER_CUSTOMER_INTERFACE;

            case \XLite::MAIL_INTERFACE:
                return self::THEME_TWEAKER_MAIL_INTERFACE . '/' . $this->getInnerInterface();

            default:
                return null;
        }
    }

    protected function isResourceAvailableByPath($path)
    {
        $result = file_exists($path);

        if ($result && strpos($path, LC_DIR_SKINS . static::THEME_TWEAKER_CUSTOMER_INTERFACE) === 0) {
            return false;
        }

        return $result;
    }

    protected function isAdminSidebarFirstVisible()
    {
        return (!\XLite::getController() instanceof NotificationEditor)
            && parent::isAdminSidebarFirstVisible();
    }
}
