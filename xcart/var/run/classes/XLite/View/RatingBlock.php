<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Vote bar widget
 */
class RatingBlock extends \XLite\View\AView
{
    /**
     * Widget param names
     */
    const PARAM_RATE        = 'rate';
    const PARAM_MAX         = 'max';
    const PARAM_COUNT       = 'count';
    const PARAM_COUNT_NAME  = 'countName';
    const PARAM_COMMENT     = 'comment';

    const PARAM_SHOW_RATE_VALUE      = 'showRateValue';
    const PARAM_USE_COMMENT_AS_LABEL = 'useCommentAsLabel';

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = array(
            'file'  => 'rating_block/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        );

        return $list;
    }


    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'rating_block/body.twig';
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
            self::PARAM_RATE                 => new \XLite\Model\WidgetParam\TypeFloat('', 0),
            self::PARAM_MAX                  => new \XLite\Model\WidgetParam\TypeInt('', 5),
            self::PARAM_COUNT                => new \XLite\Model\WidgetParam\TypeInt('', 0),
            self::PARAM_COUNT_NAME           => new \XLite\Model\WidgetParam\TypeString('', ''),
            self::PARAM_COMMENT              => new \XLite\Model\WidgetParam\TypeString('', ''),
            self::PARAM_SHOW_RATE_VALUE      => new \XLite\Model\WidgetParam\TypeBool('', false),
            self::PARAM_USE_COMMENT_AS_LABEL => new \XLite\Model\WidgetParam\TypeBool('', false),
        );
    }

    /**
     * Get rating
     *
     * @return float
     */
    protected function getRating()
    {
        return $this->getParam(self::PARAM_RATE);
    }

    public function showRateValue()
    {
        return $this->getParam(self::PARAM_SHOW_RATE_VALUE);
    }
    /**
     * Get max
     *
     * @return integer
     */
    protected function getMax()
    {
        return $this->getParam(self::PARAM_MAX);
    }

    /**
     * Get count
     *
     * @return integer
     */
    protected function getCount()
    {
        return $this->getParam(self::PARAM_COUNT);
    }

    /**
     * Get count
     *
     * @return integer
     */
    protected function getCountName()
    {
        return $this->getParam(self::PARAM_COUNT_NAME);
    }

    /**
     * Get comment
     *
     * @return string
     */
    protected function getComment()
    {
        return $this->getParam(self::PARAM_COMMENT);
    }
}
