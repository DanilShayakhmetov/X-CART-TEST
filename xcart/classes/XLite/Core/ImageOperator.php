<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use XLite\Core\ImageOperator\ADTO;
use XLite\Core\ImageOperator\AEngine;

/**
 * Image operator
 */
class ImageOperator extends \XLite\Base\SuperClass
{
    const ENGINE_IMAGE_MAGICK = 'image_magick';
    const ENGINE_GD           = 'gd';
    const ENGINE_SIMPLE       = 'simple';

    /**
     * Engine
     *
     * @var AEngine
     */
    protected static $engine;

    /**
     * @var string
     */
    protected static $engineType;

    /**
     * @var ADTO
     */
    protected $image;

    /**
     * Get engine
     *
     * @return AEngine
     */
    protected static function getEngine()
    {
        if (self::$engine === null) {
            if (ImageOperator\Engine\ImageMagick::isEnabled()) {
                static::$engine = new ImageOperator\Engine\ImageMagick();
                static::$engineType = self::ENGINE_IMAGE_MAGICK;

            } elseif (ImageOperator\Engine\GD::isEnabled()) {
                static::$engine = new ImageOperator\Engine\GD();
                static::$engineType = self::ENGINE_GD;

            } else {
                static::$engine = new ImageOperator\Engine\Simple();
                static::$engineType = self::ENGINE_SIMPLE;
            }
        }

        return static::$engine;
    }

    /**
     * Return engine type
     *
     * @return string
     */
    public static function getEngineType()
    {
        static::getEngine();

        return self::$engineType;
    }

    /**
     * Get cropped dimensions
     *
     * @param integer $width     Original width
     * @param integer $height    Original height
     * @param integer $maxWidth  Maximum width
     * @param integer $maxHeight Maximum height
     *
     * @return array (new width & height)
     */
    public static function getCroppedDimensions($width, $height, $maxWidth, $maxHeight)
    {
        $maxWidth  = max(0, (int) $maxWidth);
        $maxHeight = max(0, (int) $maxHeight);

        $isWidth = $width > 0;
        $isHeight = $height > 0;

        $resultWidth = $isWidth ? $width : $maxWidth;
        $resultHeight = $isHeight ? $height : $maxHeight;

        $isMaxWidth = $maxWidth > 0;
        $isMaxHeight = $maxHeight > 0;

        if ($isWidth && $isHeight && ($isMaxWidth || $isMaxHeight)) {
            if ($isMaxWidth && $isMaxHeight) {
                $widthFactor = $width > $maxWidth ? $maxWidth / $width : 1;
                $heightFactor = $height > $maxHeight ? $maxHeight / $height : 1;
                $factor = $widthFactor < $heightFactor ? $widthFactor : $heightFactor;

            } elseif ($isMaxWidth) {
                $factor = $width > $maxWidth ? $maxWidth / $width : 1;

            } else {
                $factor = $height > $maxHeight ? $maxHeight / $height : 1;
            }

            $resultWidth = max(1, round($factor * $width, 0));
            $resultHeight = max(1, round($factor * $height, 0));
        }

        return [
            $resultWidth !== 0 ? $resultWidth : null,
            $resultHeight !== 0 ? $resultHeight : null
        ];
    }

    /**
     * Constructor
     *
     * @param mixed $image
     */
    public function __construct($image)
    {
        $dto = ADTO::getDTO($image);

        $this->setImage($dto);
        static::getEngine()->setImage($this->image);
    }

    /**
     * Resize by limits
     *
     * @param integer $width  Width top limit OPTIONAL
     * @param integer $height Height top limit OPTIONAL
     *
     * @return array New width, new height and operation result
     */
    public function resize($width = null, $height = null)
    {
        $image = $this->getImage();

        return ($width !== $image->getWidth() || $height !== $image->getHeight())
            ? array($width, $height, static::getEngine()->resize($width, $height))
            : array($width, $height, false);
    }

    /**
     * Resize by multiple sizes
     *
     * @param array $sizes
     *
     * @return array origin sizes array with temporary file objects
     */
    public function resizeBulk($sizes)
    {
        return static::getEngine()->resizeBulk($sizes);
    }

    /**
     * @param float $degree
     *
     * @return bool
     */
    public function rotate($degree)
    {
        return static::getEngine()->rotate($degree);
    }

    /**
     * @param bool $horizontal
     *
     * @return bool
     */
    public function mirror($horizontal = true)
    {
        return static::getEngine()->mirror($horizontal);
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
