<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Tooltip widget
 */
class Tooltip extends \XLite\View\AView
{
    /**
     * Widget parameter names
     */
    const PARAM_ID            = 'id';
    const PARAM_TEXT          = 'text';
    const PARAM_WIDGET        = 'helpWidget';
    const PARAM_PLACEMENT     = 'placement';
    const PARAM_DELAY         = 'delay';
    const PARAM_DELAY_SHOW    = 'delayShow';
    const PARAM_CLASS         = 'className';
    const PARAM_CAPTION       = 'caption';
    const PARAM_IS_IMAGE_TAG  = 'isImageTag';
    const PARAM_IMAGE_CLASS   = 'imageClass';
    const PARAM_CLEAR_AFTER   = 'clear';
    const PARAM_HELP_ID       = 'helpId';
    const PARAM_CONTAINER     = 'container';
    const PARAM_KEEP_ON_HOVER = 'keepOnHover';
    const PARAM_IS_HTML_MODE  = 'isHtmlMode';

    const ATTR_CLASS = 'class';
    const ATTR_ID    = 'id';

    const CAPTION_CSS_CLASS = 'tooltip-caption';

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS] = array_merge($list[static::RESOURCE_JS], static::getVueLibraries());

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/tooltip.twig';
    }

    /**
     * Keep on hover
     *
     * @return boolean
     */
    protected function isKeepOnHover()
    {
        return $this->getParam(static::PARAM_KEEP_ON_HOVER);
    }

    /**
     * Get trigger
     *
     * @return string
     */
    protected function getTrigger()
    {
        return $this->isKeepOnHover()
            ? 'manual'
            : 'hover';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_TEXT          => new \XLite\Model\WidgetParam\TypeString('Text to show in tooltip', ''),
            static::PARAM_WIDGET        => new \XLite\Model\WidgetParam\TypeString('Widget to show in tooltip', ''),
            static::PARAM_PLACEMENT     => new \XLite\Model\WidgetParam\TypeString('Tooltip placement', 'top auto'),
            static::PARAM_DELAY         => new \XLite\Model\WidgetParam\TypeInt('Tooltip hide delay', 0),
            static::PARAM_DELAY_SHOW    => new \XLite\Model\WidgetParam\TypeInt('Tooltip show delay', 0),
            static::PARAM_KEEP_ON_HOVER => new \XLite\Model\WidgetParam\TypeBool('Keep on hover', true),
            static::PARAM_ID            => new \XLite\Model\WidgetParam\TypeString('ID of element', ''),
            static::PARAM_CLASS         => new \XLite\Model\WidgetParam\TypeString('CSS class for caption', ''),
            static::PARAM_CAPTION       => new \XLite\Model\WidgetParam\TypeString('Caption', ''),
            static::PARAM_IS_IMAGE_TAG  => new \XLite\Model\WidgetParam\TypeBool('Is it shown as image?', true),
            static::PARAM_IMAGE_CLASS   => new \XLite\Model\WidgetParam\TypeString('CSS class of image', ''),
            static::PARAM_CLEAR_AFTER   => new \XLite\Model\WidgetParam\TypeBool('Should we insert clear after tooltip', true),
            static::PARAM_HELP_ID       => new \XLite\Model\WidgetParam\TypeString('ID of element contaning help text', ''),
            static::PARAM_CONTAINER     => new \XLite\Model\WidgetParam\TypeString('Container of tooltip', ''),
            static::PARAM_IS_HTML_MODE  => new \XLite\Model\WidgetParam\TypeBool('Is it shown as html?', true),
        ];
    }

    /**
     * Checks if image must be shown
     *
     * @return boolean
     */
    protected function isImageTag()
    {
        return $this->getParam(static::PARAM_IS_IMAGE_TAG);
    }

    /**
     * Checks if html-content must be shown
     *
     * @return boolean
     */
    protected function isHtmlMode()
    {
        return $this->getParam(static::PARAM_IS_HTML_MODE);
    }

    /**
     * Define array of attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $attrs = [
            static::ATTR_CLASS => $this->getClass(),
        ];

        $attrs += $this->getParam(static::PARAM_ID)
                ? [self::ATTR_ID => $this->getParam(static::PARAM_ID)]
                : [];

        return $attrs;
    }

    /**
     * Return HTML representation for widget attributes
     *
     * @return string
     */
    protected function getAttributesCode()
    {
        return ' ' . static::convertToHtmlAttributeString($this->getAttributes());
    }

    /**
     * Define CSS class of caption text
     *
     * @return string
     */
    protected function getClass()
    {
        return static::CAPTION_CSS_CLASS
            . ($this->isImageTag() ? ' ' . $this->getImageCSSClass() . ' ' : ' ')
            . $this->getParam(static::PARAM_CLASS);
    }

    /**
     * Get image CSS classes
     *
     * @return string
     */
    protected function getImageCSSClass()
    {
        return $this->getParam(static::PARAM_IMAGE_CLASS)
            ?: 'icon-tooltip fa fa-question-circle';
    }

    /**
     * Get ID of element containing help text
     *
     * @return string
     */
    protected function getHelpId()
    {
        return $this->getParam(static::PARAM_HELP_ID);
    }

    /**
     * Get ID of element containing help text
     *
     * @return string
     */
    protected function getDelay()
    {
        $delayShow = (int) $this->getParam(static::PARAM_DELAY_SHOW);
        $delayHide = (int) $this->getParam(static::PARAM_DELAY);

        if ($delayShow === $delayHide) {
            $result = $delayShow;

        } else {
            $result = json_encode(['show' => $delayShow, 'hide' => $delayHide]);
        }

        return $result;
    }
}
