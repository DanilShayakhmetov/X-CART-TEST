<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ImageOperator;

/**
 * Abstract image operator engine
 */
abstract class AEngine extends \XLite\Base\Singleton
{
    /**
     * @var ADTO
     */
    protected $image;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Resize
     *
     * @param integer $width  Width
     * @param integer $height Height
     *
     * @return boolean
     */
    abstract public function resize($width, $height);

    /**
     * Resize
     *
     * @param array $sizes
     *
     * @return array
     */
    abstract public function resizeBulk($sizes);

    /**
     * Rotate
     *
     * @param float $degree
     *
     * @return boolean
     */
    abstract public function rotate($degree);

    /**
     * Mirror
     *
     * @param boolean $horizontal
     *
     * @return boolean
     */
    abstract public function mirror($horizontal = true);

    /**
     * Check - enabled engine or not
     *
     * @return boolean
     */
    public static function isEnabled()
    {
        return true;
    }

    public function __construct()
    {
        $this->options['progressive'] = (bool) \XLite\Core\ConfigParser::getOptions(['images', 'make_progressive']);
        $this->options['resize_quality'] = (int) \XLite\Core\Config::getInstance()->Performance->resize_quality ?: 85;
    }

    /**
     * @return ADTO
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param ADTO $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
}
