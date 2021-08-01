<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\Button;

/**
 * Simple button
 */
class ThemeTweakerTab extends \XLite\View\Button\AButton
{
    const PARAM_SVG_ICON    = 'svg';
    const PARAM_DISABLED_TOOLTIP = 'disabledTooltip';

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/button/themetweaker-tab.twig';
    }

    /**
     * getDefaultStyle
     *
     * @return string
     */
    protected function getDefaultButtonClass()
    {
        return '';
    }

    /**
     * Define the button type (btn-warning and so on)
     *
     * @return string
     */
    protected function getDefaultButtonType()
    {
        return 'themetweaker-button';
    }

    /**
     * @return string
     */
    protected function getDefaultStyle()
    {
        return 'themetweaker-tab';
    }

    /**
     * @return string
     */
    protected function getWrapperClass()
    {
        return 'themetweaker-tab-wrapper';
    }

    /**
     * @return string
     */
    protected function getSvgIcon()
    {
        return $this->getParam(self::PARAM_SVG_ICON);
    }

    /**
     * @return string
     */
    protected function getDisabledTooltip()
    {
        return $this->getParam(self::PARAM_DISABLED_TOOLTIP);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_SVG_ICON          => new \XLite\Model\WidgetParam\TypeString('Label', '', true),
            static::PARAM_DISABLED_TOOLTIP  => new \XLite\Model\WidgetParam\TypeString('Disabled tooltip', '', true),
        );
    }

    /**
     * @return array
     */
    protected function getWrapperAttributes()
    {
        $attrs = [
            'class' => $this->getWrapperClass()
        ];

        if ($this->isDisabled() && $this->getDisabledTooltip()) {
            $attrs['data-toggle'] = 'tooltip';
            $attrs['data-placement'] = 'auto';
            $attrs['data-title'] = static::t($this->getDisabledTooltip());
            $attrs['data-html'] = 'true';
        }

        return $attrs;
    }
}
