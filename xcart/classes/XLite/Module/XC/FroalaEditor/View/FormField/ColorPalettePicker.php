<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FroalaEditor\View\FormField;


class ColorPalettePicker extends \XLite\View\FormField\Input\Text
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/FroalaEditor/form_field/input/colorPicker.js';
        $list[] = 'modules/XC/FroalaEditor/form_field/input/colorPalette.js';

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

        $list[] = 'modules/XC/FroalaEditor/form_field/input/style.less';

        return $list;
    }

    protected function getDir()
    {
        return 'modules/XC/FroalaEditor/form_field/';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'input/body.twig';
    }

    public function getDataJson()
    {
        $colors = explode(',', $this->getValue());

        $data = [
            'colors' => array_map(function($color) {
              return [ 'value' => $color ];
            }, $colors)
        ];

        return json_encode($data);
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

        $list[static::RESOURCE_JS][]  = 'colorpicker/js/colorpicker.js';
        $list[static::RESOURCE_CSS][] = 'colorpicker/css/colorpicker.css';

        return $list;
    }

    /**
     * Get default maximum size
     *
     * @return integer
     */
    protected function getDefaultMaxSize()
    {
        return 10000;
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);

        $classes[] = 'color-palette';

        return $classes;
    }
}
