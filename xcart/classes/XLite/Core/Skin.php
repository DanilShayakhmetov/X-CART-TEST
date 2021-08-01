<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module;
use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Class Skin
 * @package XLite\Core
 *
 * @method isUseCloudZoom
 * @method isUseLazyLoad
 */
class Skin extends \XLite\Base\Singleton
{
    use ExecuteCachedTrait;

    /**
     * Default skin id
     */
    const SKIN_STANDARD = 'standard';
    const COLOR_DEFAULT = 'Default';

    /**
     * Proxies the call to the current skin Main class. Always returns null if current skin is the default.
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, array $arguments = [])
    {
        $module = $this->getCurrentSkinModule();

        return $module ? $module->callClassMethod($name, $arguments) : null;
    }

    /**
     * @return Module
     */
    public function getCurrentSkinModule()
    {
        return $this->executeCachedRuntime(function () {
            $skins = Manager::getRegistry()->getSkinModules();

            /** @var Module $module */
            foreach ($skins as $module) {
                if ($module->isActiveSkin()) {
                    return $module;
                }
            }

            return null;
        });
    }

    /**
     * @param Module $module
     *
     * @return string
     */
    public function getSkinModuleName($module = null)
    {
        if ($module === null) {
            $module = $this->getCurrentSkinModule();
        }

        return $module && $module instanceof Module
            ? $module->moduleName
            : $this->getDefaultSkinName();
    }

    /**
     * @return string
     */
    public function getCurrentSkinModuleId()
    {
        return $this->getCurrentSkinModule()
            ? $this->getCurrentSkinModule()->id
            : $this->getDefaultSkinModuleId();
    }

    /**
     * @param null $module
     *
     * @return bool
     */
    public function isColorSchemesSkin($module = null)
    {
        $module = $module ?: $this->getCurrentSkinModule();

        return $module && $module->id === Module::buildId('XC', 'ColorSchemes');
    }

    /**
     * @return string
     */
    public function getDefaultSkinModuleId()
    {
        return static::SKIN_STANDARD;
    }

    /**
     * @return string
     */
    public function getDefaultSkinName()
    {
        return Translation::lbl('Standard');
    }

    /**
     * Returns available layout colors
     *
     * @param Module $module
     *
     * @return array
     */
    public function getAvailableLayoutColors($module = null)
    {
        $module = $module ?: $this->getCurrentSkinModule();

        return $module && $module !== $this->getDefaultSkinModuleId()
            ? $module->getLayoutColors()
            : [];
    }

    /**
     * Returns layout types, defined in module
     * @return array
     */
    public function getAvailableLayoutTypes()
    {
        $module     = $this->getCurrentSkinModule();
        $validTypes = Layout::getInstance()->getLayoutTypes();

        if (!$module) {
            // default skin
            return [
                Layout::LAYOUT_GROUP_DEFAULT => $validTypes,
                Layout::LAYOUT_GROUP_HOME    => $validTypes,
            ];
        }

        $types = $module->callClassMethod('getLayoutTypes', []);

        if (count($types) > 0 && is_array(array_values($types)[0])) {
            array_walk($types, function (&$group) use ($validTypes) {
                $group = array_intersect($group, $validTypes);
            });

            return $types;
        }

        return [
            Layout::LAYOUT_GROUP_DEFAULT => array_intersect($types, $validTypes),
        ];
    }

    /**
     * Returns current skin color identifier
     *
     * @param Module $module
     * @param string $color
     *
     * @return string
     */
    public function getSkinColorId($module = null, $color = '')
    {
        $module          = $module ?: $this->getCurrentSkinModule();
        $layoutColor     = $color ?: Config::getInstance()->Layout->color;
        $availableColors = $this->getAvailableLayoutColors($module);

        if ($availableColors) {
            if (isset($availableColors[$layoutColor])) {
                return $layoutColor;
            }

            if (!$this->isColorSchemesSkin($module)) {
                return array_keys($availableColors)[0];
            }

            return static::COLOR_DEFAULT;
        }

        return '';
    }

    /**
     * Returns current skin + color display name
     *
     * @param Module $module
     * @param string $color
     *
     * @return string
     */
    public function getSkinDisplayName($module = null, $color = '')
    {
        $module          = $module ?: $this->getCurrentSkinModule();
        $layoutColor     = $color ?: Config::getInstance()->Layout->color;
        $availableColors = $this->getAvailableLayoutColors($module);

        if ($availableColors) {
            if (isset($availableColors[$layoutColor])) {
                return $availableColors[$layoutColor];
            }

            if (!$this->isColorSchemesSkin($module)) {
                return array_shift($availableColors);
            }

            return $this->getDefaultSkinName();
        }

        return $this->getSkinModuleName($module);
    }

    /**
     * Returns skin layout preview image URL
     *
     * @param Module $module Skin module
     * @param string $color  Color
     * @param string $type   Layout type
     *
     * @return string
     */
    public function getSkinPreview($module = null, $color = '', $type = '')
    {
        return $this->getSkinPreviewUrl('preview', $module, $color, $type);
    }

    /**
     * Returns skin module preview image URL
     *
     * @param Module $module Skin module
     * @param string $color  Color
     * @param string $type   Layout type
     *
     * @return string
     */
    public function getSkinListItemPreview($module = null, $color = '', $type = '')
    {
        return $this->getSkinPreviewUrl('preview_list', $module, $color, $type);
    }

    /**
     * Returns current skin + color + layout preview image URL
     *
     * @return string
     */
    public function getCurrentLayoutPreview($group = null)
    {
        return $this->getSkinPreview(
            $this->getCurrentSkinModule(),
            $this->getSkinColorId(),
            Layout::getInstance()->getLayoutType($group)
        );
    }

    /**
     * Returns current layout images settings (sizes)
     *
     * @return array
     */
    public function getCurrentImagesSettings()
    {
        return Database::getRepo(\XLite\Model\ImageSettings::class)
            ->findByModuleName($this->getCurrentSkinModuleId());
    }

    /**
     * Returns skin module preview image URL
     *
     * @param string $prefix file prefix
     * @param Module $module Skin module
     * @param string $color  Color
     * @param string $type   Layout type
     *
     * @return string
     */
    protected function getSkinPreviewUrl($prefix, $module = null, $color = '', $type = '')
    {
        $result = null;
        $path   = $module && $module instanceof Module
            ? 'modules/' . $module->author . '/' . $module->name . '/'
            : 'images/layout/';

        $image  = $prefix . ($color ? ('_' . $color) : '') . ($type ? ('_' . $type) : '') . '.jpg';
        $result = Layout::getInstance()->getResourceWebPath($path . $image);

        if (null === $result && $color) {
            $image  = $prefix . ($color ? ('_' . $color) : '') . '.jpg';
            $result = Layout::getInstance()->getResourceWebPath($path . $image);
        }

        if (null === $result && $type) {
            $image  = $prefix . ($type ? ('_' . $type) : '') . '.jpg';
            $result = Layout::getInstance()->getResourceWebPath($path . $image);
        }

        if (null === $result) {
            $image  = $prefix . '.jpg';
            $result = Layout::getInstance()->getResourceWebPath($path . $image);
        }

        return $result ?: Layout::getInstance()->getResourceWebPath('images/layout/' . $prefix . '_placeholder.jpg');
    }
}