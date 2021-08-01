<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\FormField\Select;

use XLite\Core\Layout;

/**
 * \XLite\View\FormField\Select\AccountStatus
 */
class LayoutType extends \XLite\View\FormField\Select\Regular
{
    const PARAM_LAYOUT_GROUP = 'group';

    /**
     * @return array
     */
    public function getJSFiles()
    {
        return [
            $this->getDir() . LC_DS . 'component.js'
        ];
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        return [
            [
                'file'  => $this->getDir() . LC_DS . 'style.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            ]
        ];
    }

    /**
     * getDefaultValue
     *
     * @return string
     */
    public function getValue()
    {
        return Layout::getInstance()->getLayoutTypeByGroup($this->getParam(static::PARAM_LAYOUT_GROUP));
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            \XLite\Core\Layout::LAYOUT_TWO_COLUMNS_LEFT  => static::t('Two columns with left sidebar'),
            \XLite\Core\Layout::LAYOUT_TWO_COLUMNS_RIGHT => static::t('Two columns with right sidebar'),
            \XLite\Core\Layout::LAYOUT_THREE_COLUMNS     => static::t('Three columns'),
            \XLite\Core\Layout::LAYOUT_ONE_COLUMN        => static::t('One column'),
        );
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        $currentGroup = $this->getParam(static::PARAM_LAYOUT_GROUP);
        $groupTypes = Layout::getInstance()->getAvailableLayoutTypes();
        $availableTypes = isset($groupTypes[$currentGroup])
            ? $groupTypes[$currentGroup]
            : $groupTypes[Layout::LAYOUT_GROUP_DEFAULT];

        $options = parent::getOptions();

        $result = [];
        foreach ($options as $type => $label) {
            if (in_array($type, $availableTypes, true)) {
                $result[$type] = $label;
            }
        }

        return $result;
    }

    /**
     * Return name of the folder with templates
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ThemeTweaker/layout_type_select';
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . LC_DS . 'template.twig';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'field.twig';
    }

    /**
     * @return string
     */
    protected function getFieldLabelTemplate()
    {
        return $this->getDir() . LC_DS . 'label.twig';
    }

    /**
     * @return string
     */
    protected function getSelectedOptionLabel()
    {
        $options = $this->getOptions();

        foreach ($options as $value => $label) {
            if ($this->isOptionSelected($value)) {
                return $label;
            }
        }

        return '';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_LAYOUT_GROUP => new \XLite\Model\WidgetParam\TypeSet(
                'Layout group', Layout::LAYOUT_GROUP_DEFAULT, false, $this->getLayoutGroups()
            ),
        );
    }

    /**
     * Returns layout type image
     *
     * @param string $value Layout type
     *
     * @return string
     */
    protected function getImage($value)
    {
        return $this->getSVGImage($this->getDir() . LC_DS . 'images' . LC_DS . $value . '.svg');
    }

    /**
     * @return array
     */
    protected function getLayoutGroups()
    {
        return array_keys(Layout::getInstance()->getAvailableLayoutTypes());
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return true;
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

        $classes[] = 'hidden';

        return $classes;
    }
}
