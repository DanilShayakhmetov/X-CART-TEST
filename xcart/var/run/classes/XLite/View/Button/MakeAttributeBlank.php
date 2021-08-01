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
class MakeAttributeBlank extends \XLite\View\Button\AButton
{
    const PARAM_POPOVER_TEXT = 'popoverText';

    /**
     * Get a list of JavaScript files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'button/js/makeblank.js';

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'button/css/makeblank.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/makeblank.twig';
    }

    /**
     * Get style
     *
     * @return string
     */
    protected function  getStyle()
    {
        return 'makeblank'
            . ($this->getParam(self::PARAM_STYLE) ? ' ' . $this->getParam(self::PARAM_STYLE) : '');
    }

    /**
     * @return mixed
     */
    public function getPopoverText()
    {
        return $this->getParam(static::PARAM_POPOVER_TEXT);
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
            static::PARAM_POPOVER_TEXT     => new \XLite\Model\WidgetParam\TypeString('Popover text', ''),
        );

        $this->widgetParams[self::PARAM_LABEL]->setValue('Make blank');
    }
}
