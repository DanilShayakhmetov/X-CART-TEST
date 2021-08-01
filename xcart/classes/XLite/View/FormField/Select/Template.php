<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module;
use XLite\Core\Skin;

/**
 * \XLite\View\FormField\Select\Template
 */
class Template extends \XLite\View\FormField\Select\Regular implements \XLite\Core\PreloadedLabels\ProviderInterface
{
    const SKIN_STANDARD = 'standard';

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/template.less';

        return $list;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/js/template.js';

        return $list;
    }

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return parent::getValue() ?: Skin::getInstance()->getDefaultSkinModuleId();
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array_reduce($this->getSkinModules(), function ($acc, $module) {
            $acc[$this->getModuleId($module)] = $this->getModuleLabel($module);
            return $acc;
        }, []);
    }

    /**
     * Returns skin modules
     *
     * @return array
     */
    protected function getSkinModules()
    {
        $result = $this->getDefaultSkinModules();

        $skin_modules = Manager::getRegistry()->getSkinModules();

        /** @var Module $module */
        foreach ($skin_modules as $module) {
            $colors = $this->getSkinColors($module);

            if ($colors) {
                foreach ($colors as $color => $label) {
                    $result[] = [
                        'module' => $module,
                        'color'  => $color,
                        'label'  => $label
                    ];
                }
            } else {
                if ($module->id === 'XC-CrispWhiteSkin') {
                    $result = array_merge([['module' => $module]], $result);

                } else {
                    $result[] = [
                        'module' => $module,
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getDefaultSkinModules()
    {
        return [
            Skin::getInstance()->getDefaultSkinModuleId()
        ];
    }

    /**
     * @param Module $module
     * @return array
     */
    protected function getSkinColors($module)
    {
        return Skin::getInstance()->getAvailableLayoutColors($module);
    }

    /**
     * Returns option id
     *
     * @param array|string $skin Module
     *
     * @return string
     */
    protected function getModuleId($skin)
    {
        if (isset($skin['module'])) {
            return $skin['module']->id . (isset($skin['color']) ? ('_' . $skin['color']) : '');
        }

        return (string) $skin;
    }

    /**
     * Returns option image
     *
     * @param array|string $skin Module
     *
     * @return string
     */
    protected function getModuleImage($skin)
    {
        $module = isset($skin['module']) ? $skin['module'] : $skin;
        $color = isset($skin['color']) ? $skin['color'] : '';
        return Skin::getInstance()->getSkinListItemPreview($module, $color);
    }

    /**
     * Returns option image
     *
     * @param array|string $skin Module
     *
     * @return string
     */
    protected function getModuleLabel($skin)
    {
        $module = isset($skin['module']) ? $skin['module'] : $skin;
        $color = isset($skin['color']) ? $skin['color'] : '';
        return isset($skin['label'])
            ? $skin['label']
            : Skin::getInstance()->getSkinDisplayName($module, $color);
    }

    /**
     * Check module is selected
     *
     * @param array|string $module Module
     *
     * @return boolean
     */
    protected function isModuleSelected($module)
    {
        return $this->getModuleId($module) === (string) $this->getValue();
    }

    /**
     * Check module is recently installed
     *
     * @param array|string $module Module
     *
     * @return boolean
     */
    protected function isMarked($module)
    {
        $result = false;

        if (Skin::getInstance()->getDefaultSkinModuleId() !== $module
            && \XLite\Core\Request::getInstance()->recent
        ) {
            $installedIds = \XLite\Core\Request::getInstance()->recent;

            $result = in_array($module['module']->id, $installedIds, false);
        }

        $moduleId = \XLite\Core\Request::getInstance()->moduleId;

        if ($moduleId) {
            $result = $this->getModuleId($module) === $moduleId;
        }

        return $result;
    }

    /**
     * Check if redeploy is required
     *
     * @param array|string $skin
     * @return string
     */
    protected function isRedeployRequired($skin)
    {
        $moduleId = isset($skin['module']) ? $skin['module']->id : $skin;

        return strpos($this->getValue(), $moduleId) !== 0;
    }

    /**
     * Returns style class
     *
     * @param array|string $module Module
     *
     * @return string
     */
    protected function getStyleClass($module)
    {
        $result = 'template';

        if ($this->isModuleSelected($module)) {
            $result .= ' selected checked';
        }

        if ($this->isMarked($module)) {
            $result .= ' marked';
        }

        return $result;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'template.twig';
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
        return [
            'To make your changes visible in the customer area, cache rebuild is required. It will take several seconds. You don’t need to close the storefront, the operation is executed in the background.' => static::t('To make your changes visible in the customer area, cache rebuild is required. It will take several seconds. You don’t need to close the storefront, the operation is executed in the background.')
        ];
    }
}
