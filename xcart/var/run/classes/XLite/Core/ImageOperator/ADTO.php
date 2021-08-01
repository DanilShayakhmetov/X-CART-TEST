<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ImageOperator;

use XLite\Core\ImageOperator\DTO\Local;
use XLite\Core\ImageOperator\DTO\Model;
use XLite\Core\ImageOperator\DTO\Remote;
use XLite\Core\RemoteResource\RemoteResourceException;
use XLite\Core\RemoteResource\RemoteResourceFactory;

/**
 * Abstract image DTO
 */
abstract class ADTO
{
    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var integer
     */
    protected $width;

    /**
     * @var integer
     */
    protected $height;

    /**
     * @param $value
     *
     * @return static|null
     */
    public static function getDTO($value)
    {
        if ($value instanceof \XLite\Model\Base\Image) {

            return new Model($value);
        }

        if (static::isURL($value)) {

            try {
                return new Remote(RemoteResourceFactory::getRemoteResourceByURL($value));

            } catch (RemoteResourceException $e) {
            }
        } elseif (is_string($value)) {

            return new Local($value);
        }

        return null;
    }

    /**
     * @param $path
     *
     * @return boolean
     */
    protected static function isURL($path)
    {
        return (bool) filter_var($path, FILTER_VALIDATE_URL);
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }
}
