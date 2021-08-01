<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Remove button
 */
class Remove extends \XLite\View\Button\AButton
{
    const PARAM_IS_CROSS = 'isCrossIcon';

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'button/js/remove.js';

        return $list;
    }

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'button/css/remove.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/remove.twig';
    }

    /**
     * Get style
     *
     * @return string
     */
    protected function  getStyle()
    {
        return 'remove'
            . ($this->getParam(self::PARAM_STYLE) ? ' ' . $this->getParam(self::PARAM_STYLE) : '')
            . ($this->isCrossIcon() ? ' cross-icon' : '');
    }

    /**
     * @return boolean
     */
    public function isCrossIcon()
    {
        return $this->getParam(static::PARAM_IS_CROSS);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[self::PARAM_LABEL]->setValue('Remove');

        $this->widgetParams += [
            static::PARAM_IS_CROSS    => new \XLite\Model\WidgetParam\TypeBool('Value', false),
        ];
    }
}
